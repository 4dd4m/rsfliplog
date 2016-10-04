<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Fliplog {

    public function insertGlyph($type) {
        switch ($type) {
            case 'save':
                $icon = 'glyphicon-floppy-disk';
                break;
            case 'remove':
                $icon = 'glyphicon-remove';
                break;
            case 'review':
                $icon = 'glyphicon-eye-open';
                break;
            case 'cart':
                $icon = 'glyphicon-shopping-cart';
                break;
            case 'arrow':
                $icon = 'glyphicon-share-alt';
                break;
            case 'off':
                $icon = 'glyphicon-off';
                break;
            case 'edit':
                $icon = 'glyphicon-edit';
                break;
            case 'pencil':
                $icon = 'glyphicon-pencil';
                break;
            case 'ok':
                $icon = 'glyphicon-ok';
                break;
            case 'right-arrow':
                $icon = 'glyphicon-arrow-right';
                break;
            case 'left-arrow':
                $icon = 'glyphicon-arrow-left';
                break;            
			case 'home':
                $icon = 'glyphicon-home';
                break;
			case 'gbp':
				$icon = 'glyphicon-gbp';
				break;			
			case 'folderopen':
				$icon = 'glyphicon-folder-open';
				break;			
			case 'resize':
				$icon = 'glyphicon-resize-horizontal';
				break;
			case 'stats':
				$icon = 'glyphicon-stats';
				break;
			default:
				echo 'Invalid Glyph';
	}


        return '<span class="glyphicon ' . $icon . '" aria-hidden="true" ></span>&nbsp;';
    }

    function time_elapsed_string($ptime) {
        $etime = time() - $ptime;

        if ($etime < 1) {
            return '0 seconds';
        }

        $a = array(365 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60 => 'month',
            24 * 60 * 60 => 'day',
            60 * 60 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
        $a_plural = array('year' => 'years',
            'month' => 'months',
            'day' => 'days',
            'hour' => 'hours',
            'minute' => 'minutes',
            'second' => 'seconds'
        );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
            }
        }
    }

    public function getPlayerAvatar($userName = 'ZsuiDamm') {
        $file = file_get_contents("http://services.runescape.com/m=avatar-rs/$userName/chat.gif");

        $local_file = base_url() . "images/chat/" . $userName . ".gif";
        if (!file_exists($local_file)) {
            $image = "chat.gif";
            copy($file, $image);
            echo "exists";
        } else {
            return true;
        }
    }

}

/* End of file Someclass.php */