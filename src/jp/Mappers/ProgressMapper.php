<?php

namespace jp\Mappers;

use jp\Controllers\ProgressController;
use jp\Mappers\EntityMapper;

class ProgressMapper extends EntityMapper
{
    public function generatePlayerProgress($accountId)
    {
        $sql = 'SELECT * '
             . 'FROM results '
             . 'WHERE id = ? '
             . 'AND type = \'player\' '
             . 'ORDER BY result_time '
             . 'LIMIT 30';
        $resultsStmt = $this->db->prepare($sql);
        $resultsStmt->bind_param('i', $accountId);
        $this->db->execute($resultsStmt);
        $result = $resultsStmt->get_result();

        $pgEntityArr = [
            'damage' => [],
            'winrate' => [],
            'xp' => []
        ];
        $progress = [
            'account_id' => $accountId,
            'pvp' => $pgEntityArr,
            'pvp_div2' => $pgEntityArr,
            'pvp_div3' => $pgEntityArr,
            'pve' => $pgEntityArr,
            'club' => $pgEntityArr,
            'rank_solo' => $pgEntityArr
        ];

        while ($result !== false && ($row = $result->fetch_object()) != false) {
            $requestData = json_decode($row->data);
            $playerData = $requestData->data->{$accountId};

            if ($playerData->hidden_profile == true) {
                break;
            }

            foreach (array_keys($progress) as $gameMode) {
                if ($gameMode == 'account_id') {
                    continue;
                }

                foreach (array_keys($progress[$gameMode]) as $progressType) {
                    $this->appendStatsEntryFromResult(
                        $progress,
                        $playerData->statistics,
                        $gameMode,
                        $progressType,
                        (int)$row->result_time
                    );
                }
            }
        }

        return $progress;
    }

    /**
     * @param array $stats
     * @param stdClass $result
     * @param string $gameMode
     * @param string $progressType
     * @param int $timeStamp
     */
    protected function appendStatsEntryFromResult(&$stats, $result, $gameMode, $progressType, $timeStamp)
    {
        $battles = (int)($result->{$gameMode}->battles);
        $entry = 0;

        if ($battles == 0) {
            return;
        }

        switch ($progressType) {
            case 'damage':
                $damageDealt = (int)($result->{$gameMode}->damage_dealt);
                $entry = round(($damageDealt / $battles), 2);
                break;
            case 'winrate':
                $wins = (int)($result->{$gameMode}->wins);
                $entry = round(($wins / $battles) * 100, 2);
                break;
            case 'xp':
                $xp = (int)($result->{$gameMode}->xp);
                $entry = round(($xp / $battles), 2);
                break;
        }

        $stats[$gameMode][$progressType][] = [
            'timestamp' => $timeStamp,
            'value' => $entry
        ];
    }

    /**
     * @param int $accountId
     * @param string $progress
     */
    public function upsertProgress($accountId, $progress)
    {
        $sql = 'SELECT COUNT(1) '
             . 'FROM players '
             . 'WHERE EXISTS ( '
             . '  SELECT 1 '
             . '  FROM players '
             . '  WHERE id = '.(int)$accountId
             . ')';
        $exists = $this->db->querySingle($sql);

        $upsertTime = time();

        if ((int)reset($exists) == 1) {
            $sql = 'UPDATE players '
                 . 'SET progress_json = ? '
                 . '  , progress_time = ? '
                 . 'WHERE id = ? ';
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('sii', $progress, $upsertTime, $accountId);
        } else {
            $sql = 'INSERT INTO players (id, progress_json, progress_time)'
                 . 'VALUES (?, ?, ?)';
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('isi', $accountId, $progress, $upsertTime);
        }

        $this->db->execute($stmt);
    }
}
