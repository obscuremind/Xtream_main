<?php
class TS
{
    public static $rIndex;
    public static $rBuffer;
    public static $rPosition;
    public static $rByte;

    public function setPacket($rBuffer)
    {
        self::$rBuffer = $rBuffer;
        self::$rPosition = 7;
        self::$rIndex = 0;
        self::$rByte = ord(self::$rBuffer[self::$rIndex]);
    }

    public function getBits($rNumBits)
    {
        $rNum = 0;
        while ($rNumBits > 0) {
            $shift = $rNumBits < self::$rPosition + 1 ? $rNumBits : self::$rPosition + 1;
            $mask = (1 << $shift) - 1;
            $rNum = ($rNum << $shift) | (($self::$rByte >> (self::$rPosition + 1 - $shift)) & $mask);
            $rNumBits -= $shift;
            self::$rPosition -= $shift;

            if (self::$rPosition < 0) {
                self::$rPosition = 7;
                self::$rIndex++;
                if (self::$rIndex < strlen(self::$rBuffer)) {
                    self::$rByte = ord(self::$rBuffer[self::$rIndex]);
                } else {
                    self::$rByte = 0;
                }
            }
        }
        return $rNum;
    }

    public function parsePacket()
    {
        $rReturn = [
            "sync_byte" => self::getBits(8),
            "transport_error_indicator" => self::getBits(1),
            "payload_unit_start_indicator" => self::getBits(1),
            "transport_priority" => self::getBits(1),
            "pid" => self::getBits(13),
            "scrambling_control" => self::getBits(2),
            "adaptation_field_exist" => self::getBits(2),
            "continuity_counter" => self::getBits(4),
        ];

        if ($rReturn["adaptation_field_exist"] == 2 || $rReturn["adaptation_field_exist"] == 3) {
            $rReturn["adaptation_field_length"] = self::getBits(8);
            if ($rReturn["adaptation_field_length"] > 0) {
                $rReturn += [
                    "discontinuity_indicator" => self::getBits(1),
                    "random_access_indicator" => self::getBits(1),
                    "priority_indicator" => self::getBits(1),
                    "pcr_flag" => self::getBits(1),
                    "opcr_flag" => self::getBits(1),
                    "splicing_point_flag" => self::getBits(1),
                    "transport_private_data_flag" => self::getBits(1),
                    "adaptation_field_extension_flag" => self::getBits(1)
                ];

                if ($rReturn["pcr_flag"]) {
                    $rReturn += [
                        "program_clock_reference_base" => self::getBits(33),
                        "reserved_pcr" => self::getBits(6),
                        "program_clock_reference_extension" => self::getBits(9)
                    ];
                    $rReturn["pcr"] = ($rReturn["program_clock_reference_base"] * 300 + $rReturn["program_clock_reference_extension"]) / 300;
                }
                if ($rReturn["opcr_flag"]) {
                    $rReturn += [
                        "original_program_clock_reference_base" => self::getBits(33),
                        "reserved_opcr" => self::getBits(6),
                        "original_program_clock_reference_extension" => self::getBits(9)
                    ];
                    $rReturn["opcr"] = ($rReturn["original_program_clock_reference_base"] * 300 + $rReturn["original_program_clock_reference_extension"]) / 300;
                }
                if ($rReturn["splicing_point_flag"]) {
                    $rReturn["splice_countdown"] = self::getBits(8);
                }
                if ($rReturn["transport_private_data_flag"]) {
                    $rReturn["transport_private_data_length"] = self::getBits(8);
                    self::stepBytes($rReturn["transport_private_data_length"]);
                }
            }
        }

        if ($rReturn["pid"] == 0) {
            $rReturn["pointer_field"] = self::getBits(8);
            if ($rReturn["pointer_field"]) {
                self::stepBytes($rReturn["pointer_field"]);
            }
            $rReturn += [
                "type" => "pat",
                "table_id" => self::getBits(8),
                "section_syntax_indicator" => self::getBits(1),
                "marker" => self::getBits(1),
                "reserved_1" => self::getBits(2),
                "section_length" => self::getBits(12),
                "transport_stream_id" => self::getBits(16),
                "reserved_2" => self::getBits(2),
                "version_number" => self::getBits(5),
                "current_next_indicator" => self::getBits(1),
                "section_number" => self::getBits(8),
                "last_section_number" => self::getBits(8)
            ];
        } elseif ($rReturn["payload_unit_start_indicator"]) {
            self::$rBuffer = substr(self::$rBuffer, self::$rIndex, 188);
            self::$rIndex = 0;
            $rReturn += [
                "type" => "pes",
                "packet_start_prefix" => self::getBits(24),
                "stream_id" => self::getBits(8),
                "pes_packet_length" => self::getBits(16),
                "marker_bits" => self::getBits(2),
                "scrambling_control" => self::getBits(2),
                "priority" => self::getBits(1),
                "data_alignment_indicator" => self::getBits(1),
                "copyright" => self::getBits(1),
                "original_or_copy" => self::getBits(1),
                "pts_dts_indicator" => self::getBits(2),
                "escr_flag" => self::getBits(1),
                "es_rate_flag" => self::getBits(1),
                "dsm_trick_mode_flag" => self::getBits(1),
                "additional_copy_info_flag" => self::getBits(1),
                "crc_flag" => self::getBits(1),
                "extension_flag" => self::getBits(1),
                "pes_header_length" => self::getBits(8)
            ];

            if ($rReturn["pts_dts_indicator"] == 2 || $rReturn["pts_dts_indicator"] == 3) {
                $rReturn["pts"] = self::parsePTS();
            }
            if ($rReturn["pts_dts_indicator"] == 3) {
                $rReturn["dts"] = self::parsePTS();
            }
        }
        return $rReturn;
    }

    private function parsePTS()
    {
        $partA = self::getBits(3);
        self::getBits(1); // Marker
        $partB = self::getBits(15);
        self::getBits(1); // Marker
        $partC = self::getBits(15);
        self::getBits(1); // Marker
        return ($partA << 30) + ($partB << 15) + $partC;
    }

    public function stepBytes($rBytes)
    {
        $data = substr(self::$rBuffer, self::$rIndex, $rBytes);
        self::$rIndex += $rBytes;
        return $data;
    }
}
?>
