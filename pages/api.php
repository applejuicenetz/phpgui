<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Search;
use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\subs;

header('Content-Type: application/json');

if (empty($_SESSION['core_host'])) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

// Handle actions
if (!empty($_GET['action'])) {
    $core_action = new Core();
    switch ($_GET['action']) {
        case 'set_maxdl':
            $value = (int)($_GET['value'] ?? 0);
            $settings_cur = $core_action->command("xml", "settings.xml");
            $core_action->command("function", "setsettings?MaxDownload=" . $value
                . "&MaxUpload=" . $settings_cur["MAXUPLOAD"]["VALUES"]["CDATA"]
                . "&MaxConnections=" . $settings_cur["MAXCONNECTIONS"]["VALUES"]["CDATA"]
                . "&Speedperslot=" . $settings_cur["SPEEDPERSLOT"]["VALUES"]["CDATA"]
                . "&MaxNewConnectionsPerTurn=" . $settings_cur["MAXNEWCONNECTIONSPERTURN"]["VALUES"]["CDATA"]
                . "&MaxSourcesPerFile=" . $settings_cur["MAXSOURCESPERFILE"]["VALUES"]["CDATA"]
                . "&AutoConnect=" . $settings_cur["AUTOCONNECT"]["VALUES"]["CDATA"]);
            echo json_encode(['ok' => true]);
            exit;
        case 'set_maxul':
            $value = (int)($_GET['value'] ?? 0);
            $settings_cur = $core_action->command("xml", "settings.xml");
            $core_action->command("function", "setsettings?MaxUpload=" . $value
                . "&MaxDownload=" . $settings_cur["MAXDOWNLOAD"]["VALUES"]["CDATA"]
                . "&MaxConnections=" . $settings_cur["MAXCONNECTIONS"]["VALUES"]["CDATA"]
                . "&Speedperslot=" . $settings_cur["SPEEDPERSLOT"]["VALUES"]["CDATA"]
                . "&MaxNewConnectionsPerTurn=" . $settings_cur["MAXNEWCONNECTIONSPERTURN"]["VALUES"]["CDATA"]
                . "&MaxSourcesPerFile=" . $settings_cur["MAXSOURCESPERFILE"]["VALUES"]["CDATA"]
                . "&AutoConnect=" . $settings_cur["AUTOCONNECT"]["VALUES"]["CDATA"]);
            echo json_encode(['ok' => true]);
            exit;
    }
}

$types = explode(',', $_GET['type'] ?? 'header');
$result = [];
$core = new Core();

foreach ($types as $type) {
    switch (trim($type)) {
        case 'header':
            $modified = $core->command("xml", "modified.xml?filter=informations");
            $temp = array_keys($modified['INFORMATION']);
            $information =& $modified['INFORMATION'][$temp[0]];
            $result['header'] = [
                'credits' => subs::sizeformat($information['CREDITS']),
                'credits_negative' => $information['CREDITS'] < 0,
            ];
            break;

        case 'downloads':
            $Downloadlist = new Downloads();
            $Downloadlist->refresh_cache();
            $downloads = [];
            foreach (array_keys($Downloadlist->subdirs) as $subdir) {
                $downloadids = $Downloadlist->ids('name', $subdir);
                foreach (array_keys($downloadids) as $a) {
                    $dl = $Downloadlist->download($a);
                    $fortschritt = round(($dl['phpaj_READY'] / $dl['SIZE']) * 100, 2);
                    $rest = $dl['SIZE'] - $dl['phpaj_READY'];
                    $eta = '';
                    if (!empty($dl['phpaj_dl_speed'])) {
                        $restzeit = $rest / $dl['phpaj_dl_speed'];
                        $stunden = $restzeit / 3600;
                        if ($stunden < 24) {
                            $eta = sprintf("%02d:%02d:%02d", $stunden, ($restzeit % 3600) / 60, $restzeit % 60);
                        } else {
                            $eta = sprintf("%.1fd", $stunden / 24);
                        }
                    }
                    $downloads[$a] = [
                        'filename' => $dl['FILENAME'],
                        'status' => $Downloadlist->status($dl['phpaj_STATUS']),
                        'done_percent' => $fortschritt,
                        'rest' => subs::sizeformat($rest),
                        'eta' => $eta,
                        'speed' => subs::sizeformat($dl['phpaj_dl_speed']) . '/s',
                        'speed_raw' => (int)$dl['phpaj_dl_speed'],
                        'sources_active' => (int)$dl['phpaj_quellen_dl'],
                        'sources_queue' => (int)$dl['phpaj_quellen_queue'],
                        'sources_total' => (int)$dl['phpaj_quellen_gesamt'],
                        'pdl' => (($dl['POWERDOWNLOAD'] + 10) / 10),
                    ];
                }
            }
            $settings_dl = $core->command("xml", "settings.xml");
            $max_dl_raw = (int)$settings_dl["MAXDOWNLOAD"]["VALUES"]["CDATA"];

            $result['downloads'] = [
                'items' => $downloads,
                'max_speed_raw' => $max_dl_raw,
                'max_speed_formatted' => $max_dl_raw > 0 ? subs::sizeformat($max_dl_raw, 2, true) : '',
            ];
            break;

        case 'uploads':
            $Uploadlist = new Uploads();
            $Sharelist = new Share();
            $icon_img = new Icons();
            $Uploadlist->refresh_cache();
            $uploads = [];
            $all_upload_ids = array_merge(
                $Uploadlist->cache['phpaj_ids_ul'] ?? [],
                $Uploadlist->cache['phpaj_ids_queue'] ?? []
            );
            foreach ($all_upload_ids as $a) {
                $ul = $Uploadlist->get_upload($a);
                $share = $Sharelist->get_file($ul['SHAREID']);
                $is_active = $ul['STATUS'] === "1";
                if ($is_active) {
                    $fortschritt = number_format(
                        (($ul['ACTUALUPLOADPOSITION'] - $ul['UPLOADFROM']) /
                         ($ul['UPLOADTO'] - $ul['UPLOADFROM'])) * 100, 2);
                    $geladen = subs::sizeformat($ul['ACTUALUPLOADPOSITION'] - $ul['UPLOADFROM']);
                    $progress_label = $fortschritt . '%';
                    $progress_sub = $geladen . ' - ' . subs::sizeformat($ul['UPLOADTO'] - $ul['UPLOADFROM']);
                    $progress_width = (float)$fortschritt;
                    $icon = $icon_img->directstate[$ul['DIRECTSTATE']];
                } else {
                    $ul_timediff = isset($ul['LASTCONNECTION'])
                        ? ($Uploadlist->cache['TIME']['VALUES']['CDATA'] - $ul['LASTCONNECTION']) / 1000
                        : 0;
                    $progress_label = sprintf("%dmin %02ds", $ul_timediff / 60, $ul_timediff % 60);
                    $progress_sub = subs::sizeformat($ul['UPLOADTO'] - $ul['UPLOADFROM']);
                    $progress_width = 0;
                    $icon = $icon_img->directstate['WAIT'] ?? '';
                }
                $uploads[$a] = [
                    'filename' => $share['SHORTFILENAME'] ?? '',
                    'nick' => $ul['NICK'] ?? '',
                    'status_html' => subs::UploadStatus($ul['STATUS']),
                    'progress_label' => $progress_label,
                    'progress_sub' => $progress_sub,
                    'progress_width' => $progress_width,
                    'speed' => subs::sizeformat($ul['SPEED']) . '/s',
                    'speed_raw' => (int)$ul['SPEED'],
                    'icon' => $icon,
                ];
            }
            $settings_ul = $core->command("xml", "settings.xml");
            $max_ul_raw = (int)$settings_ul["MAXUPLOAD"]["VALUES"]["CDATA"];
            $result['uploads'] = [
                'items' => $uploads,
                'count' => count($all_upload_ids),
                'max_speed_raw' => $max_ul_raw,
                'max_speed_formatted' => $max_ul_raw > 0 ? subs::sizeformat($max_ul_raw, 2, true) : '',
            ];
            break;

        case 'dashboard':
            $Servers = new Server();
            $Uploadlist_dash = new Uploads();
            $Downloadlist_dash = new Downloads();

            $modified_dash = $core->command("xml", "modified.xml?filter=informations");
            $temp_dash = array_keys($modified_dash['INFORMATION']);
            $info_dash =& $modified_dash['INFORMATION'][$temp_dash[0]];

            // Downloads count
            $Downloadlist_dash->refresh_cache();
            $dl_count_str = '0';
            $downloadids_all = [];
            foreach (array_keys($Downloadlist_dash->subdirs) as $a) {
                $downloadids_all = $Downloadlist_dash->ids("status", $a);
            }
            if (!empty($downloadids_all)) {
                $str = ["0", "0_1", "1", "12", "13", "15", "16", "17", "14", "18"];
                $str2 = ["14"];
                $all = count(array_diff($downloadids_all, $str2));
                $load = count(array_diff($downloadids_all, $str));
                $dl_count_str = $load . '/' . $all;
            }

            // Uploads count
            $Uploadlist_dash->refresh_cache();

            // Server info
            $info_srv = $Servers->info();

            // Share info
            $Sharelist_dash = new Share();
            $share_size = '';
            $share_count = 0;
            if ($_ENV['GUI_SHOW_SHARE']) {
                $Sharelist_dash->refresh_cache(30);
            }
            if (!empty($_SESSION['phpaj']['share_LASTTIMESTAMP'])) {
                foreach (array_keys($Sharelist_dash->cache['SHARES']['VALUES']['SHARE']) as $a) {
                    $share_count++;
                    $share_size_raw = ($share_size_raw ?? 0) + $Sharelist_dash->cache['SHARES']['VALUES']['SHARE'][$a]['SIZE'];
                }
                $share_size = subs::sizeformat($share_size_raw);
            }

            $result['dashboard'] = [
                'downloads' => $dl_count_str,
                'uploads' => (int)$Uploadlist_dash->cache['phpaj_ul'],
                'credits' => subs::sizeformat($info_dash['CREDITS']),
                'credits_negative' => $info_dash['CREDITS'] < 0,
                'dl_speed' => subs::sizeformat($info_dash['DOWNLOADSPEED']),
                'ul_speed' => subs::sizeformat($info_dash['UPLOADSPEED']),
                'session_dl' => subs::sizeformat($info_dash['SESSIONDOWNLOAD']),
                'session_ul' => subs::sizeformat($info_dash['SESSIONUPLOAD']),
                'open_conn' => $info_srv['OPENCONNECTIONS'] ?? '',
                'connected' => sprintf("%dh %dmin",
                    $Servers->netstats['timeconnected'] / 3600,
                    ($Servers->netstats['timeconnected'] % 3600) / 60),
                'share_size' => $share_size,
                'share_count' => $share_count,
            ];
            break;

        case 'search':
            $SearchApi = new Search();
            $SearchApi->refresh_cache();
            $SearchApi->process_results();

            $searches = [];
            $any_running = false;
            if (!empty($SearchApi->cache['SEARCH'])) {
                foreach (array_keys($SearchApi->cache['SEARCH']) as $sid) {
                    $s = $SearchApi->cache['SEARCH'][$sid];
                    $running = ($s['RUNNING'] === 'true');
                    if ($running) $any_running = true;
                    $total = (int)($s['SUMSEARCHES'] ?? 0) + (int)($s['OPENSEARCHES'] ?? 0);
                    $progress = $total > 0 ? round(((int)$s['SUMSEARCHES'] * 100) / $total, 2) : 100;
                    $searches[$sid] = [
                        'text' => $s['SEARCHTEXT'],
                        'running' => $running,
                        'found_files' => (int)($s['phpaj_FOUNDFILES'] ?? 0),
                        'progress' => $progress,
                    ];
                }
            }

            $entries = [];
            if (!empty($SearchApi->cache['SEARCHENTRY'])) {
                foreach (array_keys($SearchApi->cache['SEARCHENTRY']) as $eid) {
                    $e = $SearchApi->cache['SEARCHENTRY'][$eid];
                    $filename = $e['phpaj_FILENAME'];
                    $ajfsp_raw = "ajfsp://file|" . $filename . "|" . $e['CHECKSUM'] . "|" . $e['SIZE'] . "/";
                    $ajfsp_html = "ajfsp://file|" . addslashes(htmlspecialchars($filename))
                                . "|" . $e['CHECKSUM'] . "|" . $e['SIZE'] . "/";
                    $rel_info = '';
                    if (!empty($_ENV['REL_INFO'])) {
                        $rel_info = '<a target="_blank" href="' . sprintf($_ENV['REL_INFO'], $ajfsp_html)
                                  . '"><i class="fa fa-info-circle text-primary"></i></a>';
                    }
                    $entries[$eid] = [
                        'filename' => $filename,
                        'size' => subs::sizeformat($e['SIZE']),
                        'format' => $e['phpaj_FORMAT'],
                        'sources' => (int)$e['phpaj_COUNT'],
                        'search_id' => $e['SEARCHID'],
                        'ajfsp_link' => $ajfsp_raw,
                        'rel_info' => $rel_info,
                    ];
                }
            }

            $result['search'] = [
                'any_running' => $any_running,
                'total_count' => (int)($SearchApi->cache['SEARCHENTRY_count'] ?? 0),
                'searches' => $searches,
                'entries' => $entries,
            ];
            break;
    }
}

echo json_encode($result);
