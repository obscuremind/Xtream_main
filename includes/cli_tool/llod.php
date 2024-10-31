<?php
if (posix_getpwuid(posix_geteuid())['name'] == 'xtreamcodes') {
    if ($argc && $argc > 3) {
        $streamID = intval($argv[1]);
        $streamSources = json_decode(base64_decode($argv[2]), true);
        $streamArguments = json_decode(base64_decode($argv[3]), true);

        define('XTREAMCODES_HOME', '/home/xtreamcodes/');
        define('STREAMS_PATH', XTREAMCODES_HOME . 'content/streams/');
        define('INCLUDES_PATH', XTREAMCODES_HOME . 'includes/');
        define('CACHE_TMP_PATH', XTREAMCODES_HOME . 'tmp/cache/');
        define('CONS_TMP_PATH', XTREAMCODES_HOME . 'tmp/opened_cons/');
        define('FFMPEG', XTREAMCODES_HOME . 'bin/ffmpeg_bin/4.0/ffmpeg');
        define('FFPROBE', XTREAMCODES_HOME . 'bin/ffmpeg_bin/4.0/ffprobe');
        define('PAT_HEADER', "°\r");
        define('KEYFRAME_HEADER', "\x07P");
        define('PACKET_SIZE', 188);
        define('BUFFER_SIZE', 12032);
        define('PAT_PERIOD', 2);
        define('TIMEOUT', 20);
        define('TIMEOUT_READ', 1);

        if (file_exists(CACHE_TMP_PATH . 'settings')) {
            checkRunning($streamID);
            register_shutdown_function('shutdown');
            set_time_limit(0);
            error_reporting(E_ERROR);
            cli_set_process_title('LLOD[' . $streamID . ']');
            require INCLUDES_PATH . 'ts.php';

            $fp = $segmentFile = null;
            $segmentDuration = $segmentStatus = [];
            $settings = igbinary_unserialize(file_get_contents(CACHE_TMP_PATH . 'settings'));
            $segListSize = $settings['seg_list_size'];
            $segDeleteThreshold = $settings['seg_delete_threshold'];
            $requestPrebuffer = $settings['request_prebuffer'];
            $lastPTS = $curPTS = null;

            startLLOD($streamID, $streamSources, $streamArguments, $requestPrebuffer, $segListSize, $segDeleteThreshold);
        } else {
            echo "Settings not cached!\n";
            exit(0);
        }
    } else {
        echo "LLOD cannot be directly run!\n";
        exit(0);
    }
} else {
    exit("Please run as XtreamCodes!\n");
}

function deleteOldSegments($streamID, $keep, $threshold) {
    global $segmentStatus;
    $return = [];
    $currentSegment = max(array_keys($segmentStatus));
    foreach ($segmentStatus as $segmentID => $status) {
        if ($status && $segmentID < $currentSegment - ($keep + $threshold) + 1) {
            $segmentStatus[$segmentID] = false;
            @unlink(STREAMS_PATH . $streamID . '_' . $segmentID . '.ts');
        } elseif ($segmentID != $currentSegment) {
            $return[] = $segmentID;
        }
    }
    return count($return) > $keep ? array_slice($return, count($return) - $keep, $keep) : $return;
}

function updateSegments($streamID, $segmentsRemaining) {
    global $segmentDuration, $lastPTS, $curPTS;
    $hls = "#EXTM3U\n#EXT-X-VERSION:3\n#EXT-X-TARGETDURATION:4\n#EXT-X-MEDIA-SEQUENCE:";
    $sequence = false;

    foreach ($segmentsRemaining as $segment) {
        if (file_exists(STREAMS_PATH . $streamID . '_' . $segment . '.ts')) {
            $hls .= $sequence ? '' : $segment . "\n";
            $sequence = true;
            $segmentDuration[$segment] = $segmentDuration[$segment] ?? (($curPTS - $lastPTS) / 90000) ?: 10;
            $hls .= "#EXTINF:" . round($segmentDuration[$segment], 0) . ".000000,\n" . $streamID . '_' . $segment . ".ts\n";
        }
    }
    file_put_contents(STREAMS_PATH . $streamID . '_.m3u8', $hls);
}

function writeError($streamID, $error) {
    echo $error . "\n";
    file_put_contents(STREAMS_PATH . $streamID . '.errors', $error . "\n", FILE_APPEND | LOCK_EX);
}

function startLLOD($streamID, $streamSources, $streamArguments, $requestPrebuffer, $segListSize, $segDeleteThreshold) {
    global $segmentStatus, $segmentFile, $fp, $curPTS, $lastPTS;

    if (!file_exists(CONS_TMP_PATH . $streamID . '/')) {
        mkdir(CONS_TMP_PATH . $streamID);
    }

    $userAgent = $streamArguments['user_agent']['value'] ?? $streamArguments['user_agent']['argument_default_value'] ?? 'Mozilla/5.0';
    $options = [
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true],
        'http' => ['method' => 'GET', 'user_agent' => $userAgent, 'timeout' => TIMEOUT, 'header' => '']
    ];

    if (isset($streamArguments['proxy'])) {
        $options['http']['proxy'] = 'tcp://' . $streamArguments['proxy']['value'];
        $options['http']['request_fulluri'] = true;
    }
    if (isset($streamArguments['cookie'])) {
        $options['http']['header'] .= 'Cookie: ' . $streamArguments['cookie']['value'] . "\r\n";
    }
    if ($requestPrebuffer) {
        $options['http']['header'] .= 'X-XTREAMCODES-Prebuffer: 1' . "\r\n";
    }
    $context = stream_context_create($options);
    $fp = getActiveStream($streamID, $streamSources, $context);

    if ($fp) {
        shell_exec('rm -f ' . STREAMS_PATH . intval($streamID) . '_*.ts');
        $excessBuffer = $prebuffer = $buffer = '';
        $patHeaders = [];
        $newSegment = $pat = false;
        $firstWrite = true;
        $lastPacket = time();
        $lastSegment = round(microtime(true) * 1000);
        $segment = 0;
        $segmentFile = fopen(STREAMS_PATH . $streamID . '_' . $segment . '.ts', 'wb');
        $segmentStatus[$segment] = true;
        echo 'PID: ' . getmypid() . "\n";

        while (!feof($fp)) {
            stream_set_timeout($fp, TIMEOUT_READ);
            $buffer .= $excessBuffer . fread($fp, BUFFER_SIZE - strlen($buffer . $excessBuffer));
            $excessBuffer = '';
            $packetNum = floor(strlen($buffer) / PACKET_SIZE);

            if ($packetNum > 0) {
                $lastPacket = time();
                $excessBuffer = strlen($buffer) !== $packetNum * PACKET_SIZE ? substr($buffer, $packetNum * PACKET_SIZE) : '';
                $buffer = substr($buffer, 0, $packetNum * PACKET_SIZE);

                foreach (str_split($buffer, PACKET_SIZE) as $packet) {
                    list(, $header) = unpack('N', substr($packet, 0, 4));
                    $sync = $header >> 24 & 255;

                    if ($sync == 71) {
                        if (substr($packet, 6, 4) === PAT_HEADER) {
                            $pat = true;
                            $patHeaders = [];
                        } else {
                            $adaptationField = $header >> 4 & 3;
                            if (($adaptationField & 2) === 2) {
                                if (count($patHeaders) > 0 && unpack('C', $packet[4])[1] == 7 && substr($packet, 4, 2) === KEYFRAME_HEADER) {
                                    $prebuffer = implode('', $patHeaders);
                                    $newSegment = true;
                                    $pat = false;
                                    $patHeaders = [];

                                    $tsHandler = new TS();
                                    $tsHandler->setPacket($packet);
                                    $packetInfo = $tsHandler->parsePacket();

                                    if (isset($packetInfo['pts'])) {
                                        $lastPTS = $curPTS;
                                        $curPTS = $packetInfo['pts'];
                                    }
                                    unset($tsHandler);
                                }
                            }
                        }

                        if ($pat && count($patHeaders) < 10) {
                            $patHeaders[] = $packet;
                        }

                        if ($newSegment) {
                            $prebuffer .= $packet;
                        } else {
                            fwrite($segmentFile, $packet);
                        }
                    } else {
                        writeError($streamID, '[LLOD] No sync byte detected! Stream is out of sync.');
                        resyncStream($fp, $streamID);
                    }
                }

                if ($newSegment) {
                    $lastSegment = round(microtime(true) * 1000);
                    $position = strpos($buffer, $prebuffer);

                    if ($position > 0) {
                        $lastBuffer = substr($buffer, 0, $position);
                        if (!$firstWrite) {
                            fwrite($segmentFile, $lastBuffer);
                        }
                    }

                    if (!$firstWrite) {
                        fclose($segmentFile);
                        $segment++;
                        $segmentFile = fopen(STREAMS_PATH . $streamID . '_' . $segment . '.ts', 'wb');
                        $segmentStatus[$segment] = true;

                        $segmentsRemaining = deleteOldSegments($streamID, $segListSize, $segDeleteThreshold);
                        updateSegments($streamID, $segmentsRemaining);
                    }
                    $firstWrite = false;
                    fwrite($segmentFile, $prebuffer);
                    $prebuffer = '';
                    $newSegment = false;
                }

                $buffer = '';
            }
        }
        fclose($segmentFile);
        fclose($fp);
    }
}

function getActiveStream($streamID, $urls, $context) {
    foreach ($urls as $url) {
        $fp = @fopen($url, 'rb', false, $context);
        if ($fp) {
            $metadata = stream_get_meta_data($fp);
            $headers = [];
            foreach ($metadata['wrapper_data'] as $line) {
                if (strpos($line, 'HTTP') !== 0) {
                    list($key, $value) = explode(': ', $line);
                    $headers[$key] = $value;
                } else {
                    $headers[0] = $line;
                }
            }
            $contentType = is_array($headers['Content-Type']) ? end($headers['Content-Type']) : $headers['Content-Type'];
            if (strtolower($contentType) == 'video/mp2t') {
                return $fp;
            }
            writeError($streamID, "[LLOD] Source isn't MPEG-TS: " . $url . ' - ' . $headers['Content-Type']);
        } else {
            writeError($streamID, '[LLOD] Invalid source: ' . $url);
        }
    }
    return false;
}

function checkRunning($streamID) {
    clearstatcache(true);
    $pidFile = STREAMS_PATH . $streamID . '_.monitor';
    $pid = file_exists($pidFile) ? intval(file_get_contents($pidFile)) : null;

    if (!$pid || !file_exists('/proc/' . $pid)) {
        shell_exec("kill -9 $(ps -ef | grep 'LLOD\\[{$streamID}\\]' | grep -v grep | awk '{print \$2}')");
    } elseif (trim(file_get_contents("/proc/{$pid}/cmdline")) === "LLOD[{$streamID}]") {
        posix_kill($pid, 9);
    }
}

function shutdown() {
    global $fp, $segmentFile, $streamID;
    is_resource($segmentFile) && fclose($segmentFile);
    is_resource($fp) && fclose($fp);
}

function resyncStream($fp, $streamID) {
    $buffer = fread($fp, BUFFER_SIZE);
    if (!strpos($buffer, 'G\x01')) {
        writeError($streamID, "[LLOD] Couldn't rectify out-of-sync data. Exiting.");
        exit();
    }
    writeError($streamID, '[Loopback] Resynchronized stream. Continuing...');
}
