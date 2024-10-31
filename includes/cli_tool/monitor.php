<?php

function checkRunning($streamID) {
    clearstatcache(true);
    $monitorFile = STREAMS_PATH . $streamID . '_.monitor';
    
    if (file_exists($monitorFile)) {
        $rPID = intval(file_get_contents($monitorFile));
        if (empty($rPID)) {
            killRogueProcess($streamID);
        } else {
            $procFile = "/proc/" . $rPID;
            if (file_exists($procFile)) {
                $rCommand = trim(file_get_contents($procFile . "/cmdline"));
                if ($rCommand == "XtreamCodes[" . $streamID . "]" && is_numeric($rPID) && $rPID > 0) {
                    gracefulKill($rPID);
                }
            }
        }
    }
}

function ensureDurFileExists($streamID, $rPlaylist, $rFolder) {
    if (!fileExists(STREAMS_PATH . $streamID . '_.dur')) {
        ipTV_streaming::streamLog($streamID, SERVER_ID, 'DEBUG', "Probing stream to create .dur file...");
        $segment = ipTV_streaming::getPlaylistSegments($rPlaylist, 10)[0];

        if (!empty($segment)) {
            try {
                $streamInfo = ipTV_stream::probeStream($rFolder . $segment);
                if (intval($streamInfo['of_duration']) > 10) {
                    $streamInfo['of_duration'] = 10;
                }
                file_put_contents(STREAMS_PATH . $streamID . '_.dur', intval($streamInfo['of_duration']));
                ipTV_streaming::streamLog($streamID, SERVER_ID, 'INFO', "Created .dur file with duration: " . intval($streamInfo['of_duration']));
            } catch (Exception $e) {
                ipTV_streaming::streamLog($streamID, SERVER_ID, 'ERROR', "Failed to probe stream: " . $e->getMessage());
            }
        }
    } else {
        ipTV_streaming::streamLog($streamID, SERVER_ID, 'DEBUG', ".dur file already exists.");
    }
}

function killRogueProcess($streamID) {
    $command = "ps -ef | grep 'XtreamCodes\\[" . intval($streamID) . "\\]' | grep -v grep | awk '{print \$2}'";
    try {
        $process = proc_open($command, [], $pipes);
        if (is_resource($process)) {
            proc_close($process);
            ipTV_streaming::streamLog($streamID, SERVER_ID, 'INFO', "Killed rogue process for stream ID: $streamID");
        }
    } catch (Exception $e) {
        ipTV_streaming::streamLog($streamID, SERVER_ID, 'ERROR', "Failed to kill rogue process: " . $e->getMessage());
    }
}

function gracefulKill($pid) {
    try {
        posix_kill($pid, SIGTERM);
        sleep(1);
        if (posix_getpgid($pid)) {
            posix_kill($pid, SIGKILL);
        }
        ipTV_streaming::streamLog(null, SERVER_ID, 'INFO', "Successfully killed process with PID: $pid");
    } catch (Exception $e) {
        ipTV_streaming::streamLog(null, SERVER_ID, 'ERROR', "Failed to kill process PID: $pid - " . $e->getMessage());
    }
}

function fileExists($path) {
    if (file_exists($path)) {
        ipTV_streaming::streamLog(null, SERVER_ID, 'DEBUG', "$path exists.");
        return true;
    }
    ipTV_streaming::streamLog(null, SERVER_ID, 'ERROR', "$path not found.");
    return false;
}

if (posix_getpwuid(posix_geteuid())['name'] != 'xtreamcodes') {
    exit("Please run as XtreamCodes!\n");
}

if (!$argc || $argc <= 1) {
    exit(0);
}

$streamID = intval($argv[1]);
$restart = !empty($argv[2]);
require str_replace('\\', '/', dirname($argv[0])) . '/../../wwwdir/init.php';
checkRunning($streamID);
set_time_limit(0);
cli_set_process_title('XtreamCodes[' . $streamID . ']');

$ipTV_db->query('SELECT * FROM `streams` t1 INNER JOIN `streams_servers` t2 ON t2.stream_id = t1.id AND t2.server_id = \'%d\' WHERE t1.id = \'%d\'', SERVER_ID, $streamID);
if ($ipTV_db->num_rows() <= 0) {
    ipTV_stream::stopStream($streamID);
    exit();
}

$streamInfo = $ipTV_db->get_row();
$ipTV_db->query('UPDATE `streams_servers` SET `monitor_pid` = \'%d\' WHERE `server_stream_id` = \'%d\'', getmypid(), $streamInfo['server_stream_id']);

if (ipTV_lib::$settings['enable_cache']) {
    ipTV_streaming::updateStream($streamID);
}

$rPID = fileExists(STREAMS_PATH . $streamID . '_.pid') ? intval(file_get_contents(STREAMS_PATH . $streamID . '_.pid')) : $streamInfo['pid'];
$rAutoRestart = json_decode($streamInfo['auto_restart'], true);
$rPlaylist = STREAMS_PATH . $streamID . '_.m3u8';
$rDelayPID = $streamInfo['delay_pid'];
$rParentID = $streamInfo['parent_id'];
$streamProbe = false;
$sources = [];
$segmentTime = ipTV_lib::$SegmentsSettings['seg_time'];
$rPrioritySwitch = false;
$rMaxFails = 0;

if ($rParentID == 0) {
    $sources = json_decode($streamInfo['stream_source'], true);
}

$rCurrentSource = ($rParentID > 0) ? 'Loopback: #' . $rParentID : $streamInfo['current_source'];
$rLastSegment = $rForceSource = null;

$ipTV_db->query('SELECT t1.*, t2.* FROM `streams_options` t1, `streams_arguments` t2 WHERE t1.stream_id = \'%d\' AND t1.argument_id = t2.id', $streamID);
$streamArguments = $ipTV_db->get_rows();

if ($streamInfo['delay_minutes'] <= 0 && $streamInfo['parent_id'] == 0) {
    $rDelay = false;
    $rFolder = STREAMS_PATH;
} else {
    $rFolder = DELAY_PATH;
    $rPlaylist = DELAY_PATH . $streamID . '_.m3u8';
    $rDelay = true;
}

$rFirstRun = true;
$rTotalCalls = 0;

if (ipTV_streaming::isStreamRunning($rPID, $streamID)) {
    ipTV_streaming::streamLog($streamID, SERVER_ID, 'INFO', 'Stream is already running.');

    if ($restart) {
        $rTotalCalls = MONITOR_CALLS;

        if (is_numeric($rPID) && $rPID > 0) {
            gracefulKill($rPID);
        }

        shell_exec('rm -f ' . STREAMS_PATH . intval($streamID) . '_*');
        file_put_contents(STREAMS_PATH . $streamID . '_.monitor', getmypid());

        if ($rDelay && ipTV_streaming::isDelayRunning($rDelayPID, $streamID) && is_numeric($rDelayPID) && $rDelayPID > 0) {
            gracefulKill($rDelayPID);
        }

        usleep(50000);
        $rDelayPID = $rPID = 0;
    }
} else {
    file_put_contents(STREAMS_PATH . $streamID . '_.monitor', getmypid());
}

ensureDurFileExists($streamID, $rPlaylist, $rFolder);

if (ipTV_lib::$settings['kill_rogue_ffmpeg']) {
    exec('ps aux | grep -v grep | grep \'/' . $streamID . '_.m3u8\' | awk \'{print $2}\'', $rFFMPEG);
    foreach ($rFFMPEG as $roguePID) {
        if (is_numeric($roguePID) && $roguePID > 0 && $roguePID != $rPID) {
            gracefulKill($roguePID);
        }
    }
}

while (true) {
    if (!ipTV_streaming::isStreamRunning($rPID, $streamID)) {
        if ((0 < $streamInfo['llod']) && $streamInfo['on_demand'] && $rFirstRun) {
            if ($streamInfo['llod'] == 1) {
                $rData = ipTV_stream::startLLOD($streamID, $streamInfo, $streamInfo['parent_id'] ? array() : $streamArguments, $rForceSource);
            } else {
                $rData = ipTV_stream::startStream($streamID, false, $sources[$streamInfo['current_source']], true);
            }
        } elseif ($rParentID == 0) {
            $streamSource = $rForceSource ?: $sources[$streamInfo['current_source']];
            $rData = ipTV_stream::startStream($streamID, false, $streamSource, true);
        } else {
            $rData = ipTV_stream::startLoopback($streamID);
        }

        if (is_numeric($rData) && $rData == 0) {
            $rMaxFails++;
            if (ipTV_lib::$settings['stop_failures'] > 0 && $rMaxFails == ipTV_lib::$settings['stop_failures']) {
                ipTV_streaming::streamLog($streamID, SERVER_ID, 'ERROR', 'Failure limit reached. Exiting.');
                exit();
            }
        }

        if ($rData) {
            $rPID = intval($rData['main_pid']);
            if ($rPID) {
                file_put_contents(STREAMS_PATH . $streamID . '_.pid', $rPID);
            }

            $rPlaylist = $rData['playlist'];
            $rDelay = $rData['delay_enabled'];
            $streamInfo['delay_available_at'] = $rData['delay_start_at'];
            $rParentID = $rData['parent_id'];
            $rCurrentSource = ($rParentID > 0) ? 'Loopback: #' . $rParentID : trim($rData['stream_source'], '\'"');
            $streamProbe = true;
            ipTV_streaming::streamLog($streamID, SERVER_ID, 'INFO', "Stream started with source: $rCurrentSource");

            $ipTV_db->query('UPDATE `streams_servers` SET `stream_status` = 0, `stream_started` = \'%d\' WHERE `server_stream_id` = \'%d\'', time() - $rOffset, $streamInfo['server_stream_id']);
        } else {
            ipTV_streaming::streamLog($streamID, SERVER_ID, 'ERROR', 'Stream failed to start.');
        }
    }

    sleep(1);
}
