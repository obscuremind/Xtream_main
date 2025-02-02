<?php if (count(get_included_files()) == 1) {
    exit;
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?= htmlspecialchars($rSettings["server_name"]) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="noindex,nofollow">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/libs/jquery-nice-select/nice-select.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/jquery-toast/jquery.toast.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/treeview/style.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/clockpicker/bootstrap-clockpicker.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/nestable2/jquery.nestable.min.css" rel="stylesheet" />
    <link href="assets/libs/magnific-popup/magnific-popup.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <?php if (!$rAdminSettings["dark_mode"]) { ?>
        <link href="assets/css/app_sidebar.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <?php } else { ?>
        <link href="assets/css/app_sidebar.dark.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/bootstrap.dark.css" rel="stylesheet" type="text/css" />
    <?php } ?>
</head>

<body class="<?php if (!$rAdminSettings["dark_mode"]) {
                    echo "topbar-dark left-side-menu-light ";
                } ?><?php if (!$rAdminSettings["expanded_sidebar"]) {
                        echo 'enlarged" data-keep-enlarged="true"';
                    } else {
                        echo '"';
                    } ?>>
        <!-- Begin page -->
        <div id=" wrapper">
    <!-- Topbar Start -->
    <div class="navbar-custom">
        <ul class="list-unstyled topnav-menu float-right mb-0">
            <li class="notification-list username">
                <a class="nav-link text-white waves-effect" href="./edit_profile.php" role="button"
                    data-toggle="tooltip" data-placement="bottom" title=""
                    data-original-title="<?= $_["edit_profile"] ?>">
                    <th class="text-center"><span class="mdi mdi-account-circle-outline mdi-18px">
                            <?= $_["welcome"] ?>:&nbsp; </th>
                    <td class="text-center"><b><?= $rUserInfo["username"] ?></b></td>
                </a>
            </li>
            <?php if (($rServerError) && ($rPermissions["is_admin"]) && (hasPermissions("adv", "servers"))) { ?>
                <li class="notification-list">
                    <a href="./servers.php" class="nav-link right-bar-toggle waves-effect text-warning">
                        <i class="mdi mdi-wifi-strength-off noti-icon"></i>
                    </a>
                </li>
            <?php } ?>
            <?php if ($rPermissions["is_reseller"]) { ?>
                <li class="notification-list">
                    <a class="nav-link text-white waves-effect" href="#" role="button">
                        <i class="fe-dollar-sign noti-icon text-warning"></i>
                        <?php if (floor($rUserInfo["credits"]) == $rUserInfo["credits"]) {
                            echo number_format($rUserInfo["credits"], 0);
                        } else {
                            echo number_format($rUserInfo["credits"], 2);
                        } ?>
                    </a>
                </li>
                <?php }
            if ($rPermissions["is_admin"]) {
                if ((hasPermissions("adv", "settings")) or (hasPermissions("adv", "database")) or (hasPermissions("adv", "block_ips")) or (hasPermissions("adv", "block_isps")) or (hasPermissions("adv", "block_uas")) or (hasPermissions("adv", "categories")) or (hasPermissions("adv", "channel_order")) or (hasPermissions("adv", "epg")) or (hasPermissions("adv", "folder_watch")) or (hasPermissions("adv", "mng_groups")) or (hasPermissions("adv", "mass_delete")) or (hasPermissions("adv", "mng_packages")) or (hasPermissions("adv", "process_monitor")) or (hasPermissions("adv", "rtmp")) or (hasPermissions("adv", "subresellers")) or (hasPermissions("adv", "tprofiles"))) { ?>
                    <li class="dropdown notification-list">
                        <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect text-white" data-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="fe-settings noti-icon text-warning"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                            <?php if ((hasPermissions("adv", "settings")) or (hasPermissions("adv", "database"))) { ?>
                                <a href="./settings.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-wrench-outline mdi-rotate-90 mdi-18px"> <?= $_["settings"] ?></span></a>
                            <?php }
                            if ((hasPermissions("adv", "settings"))) { ?>
                                <a href="./cache.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-wrench-outline mdi-rotate-90 mdi-18px">
                                        <?= $_["cache_cron_redis_settings"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "mng_packages")) { ?>
                                <a href="./packages.php" class="dropdown-item notify-item"><span class="mdi mdi-package mdi-18px">
                                        <?= $_["packages"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "categories")) { ?>
                                <a href="./stream_categories.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-folder-open-outline mdi-18px"> <?= $_["categories"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "mng_groups")) { ?>
                                <a href="./groups.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-account-multiple-outline mdi-18px"> <?= $_["groups"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "epg")) { ?>
                                <a href="./epgs.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-play-protected-content mdi-18px"> <?= $_["epgs"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "channel_order")) { ?>
                                <a href="./channel_order.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-reorder-horizontal mdi-18px"> <?= $_["channel_order"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "folder_watch")) { ?>
                                <a href="./watch.php" class="dropdown-item notify-item"><span class="mdi mdi-eye-outline mdi-18px">
                                        <?= $_["folder_watch"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "subresellers")) { ?>
                                <a href="./subresellers.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-account-multiple-outline mdi-18px"> <?= $_["subresellers"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "login_flood")) { ?>
                                <a href="./flood_login.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-account-alert mdi-18px"> <span>Logins Flood</span></a>
                            <?php }
                            if (hasPermissions("adv", "security_center")) { ?>
                                <a href="./security_center.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-security mdi-18px"> Security Center</span></a>
                            <?php }
                            if (hasPermissions("adv", "block_ips")) { ?>
                                <a href="./ips.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-close-octagon-outline mdi-18px"> <?= $_["blocked_ips"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "block_isps")) { ?>
                                <a href="./isps.php" class="dropdown-item notify-item"><span class="mdi mdi-close-network mdi-18px">
                                        <?= $_["blocked_isps"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "rtmp")) { ?>
                                <a href="./rtmp_ips.php" class="dropdown-item notify-item"><span class="mdi mdi-close mdi-18px">
                                        <?= $_["rtmp_ips"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "block_uas")) { ?>
                                <a href="./useragents.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-close-box-outline mdi-18px"> <?= $_["blocked_uas"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "process_monitor")) { ?>
                                <a href="./process_monitor.php?server=<?= $_INFO["server_id"] ?>"
                                    class="dropdown-item notify-item"><span class="mdi mdi-chart-line mdi-18px">
                                        <?= $_["process_monitor"] ?></span></a>
                            <?php }
                            if (hasPermissions("adv", "tprofiles")) { ?>
                                <a href="./profiles.php" class="dropdown-item notify-item"><span
                                        class="mdi mdi-find-replace mdi-18px"> <?= $_["transcode_profiles"] ?></span></a>
                            <?php } ?>
                        </div>
                    </li>
            <?php }
            } ?>
            <li class="notification-list">
                <a href="./logout.php" class="nav-link right-bar-toggle waves-effect text-white">
                    <i class="fe-power noti-icon text-danger"></i>
                </a>
            </li>
        </ul>
        <!-- LOGO -->
        <div class="logo-box">
            <a href="<?php if ($rPermissions["is_admin"]) { ?>dashboard.php<?php } else { ?>reseller.php<?php } ?>"
                class="logo text-center">
                <span class="logo-lg">
                    <img src="<?php $rSettings["logo_url"] ? print($rSettings["logo_url"]) : print("/assets/images/logo.png") ?>"
                        alt="" height="26">
                </span>
                <span class="logo-sm">
                    <img src="<?php $rSettings["logo_url_sidebar"] ? print($rSettings["logo_url_sidebar"]) : print("/assets/images/logo-sm.png") ?>"
                        alt="" height="26">
                </span>
            </a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
            <li>
                <button class="button-menu-mobile waves-effect text-white">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </li>
        </ul>
    </div>
    <!-- end Topbar -->
    <!-- ========== Left Sidebar Start ========== -->
    <div class="left-side-menu">
        <div class="slimscroll-menu">
            <!--- Sidemenu -->
            <div id="sidebar-menu">
                <ul class="metismenu" id="side-menu">
                    <li>
                        <a
                            href="./<?php if ($rPermissions["is_admin"]) { ?>dashboard.php<?php } else { ?>reseller.php<?php } ?>"><i
                                class="mdi mdi-view-dashboard-outline mdi-18px text-purple"></i><span><?= $_["dashboard"] ?></span></a>
                    </li>
                    <?php if (($rPermissions["is_reseller"]) && ($rPermissions["reseller_client_connection_logs"])) { ?>
                        <li>
                            <a href="#"><i
                                    class="mdi mdi-information-outline mdi-18px text-danger"></i><span><?= $_["logs"] ?></span><span
                                    class="arrow-right"></span></a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="./live_connections.php"><span
                                            class="mdi mdi-account-network-outline mdi-18px"></span>
                                        <?= $_["live_connections"] ?></a></li>
                                <li><a href="./user_activity.php"><span class="mdi mdi-file-document-outline mdi-18px">
                                            <?= $_["activity_logs"] ?></a></li>
                            </ul>
                        </li>
                        <?php }
                    if ($rPermissions["is_admin"]) {
                        if ((hasPermissions("adv", "servers")) or (hasPermissions("adv", "add_server")) or (hasPermissions("adv", "live_connections")) or (hasPermissions("adv", "connection_logs"))) { ?>
                            <li>
                                <a href="#"><i
                                        class="mdi mdi-server-network mdi-18px text-warning"></i><span><?= $_["servers"] ?></span><span
                                        class="arrow-right"></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <?php if (hasPermissions("adv", "add_server")) { ?>
                                        <li><a href="./server.php"><span class="mdi mdi-upload-network-outline mdi-18px"></span>
                                                <?= $_["add_existing_lb"] ?></a></li>
                                        <li><a href="./install_server.php"><span class="mdi mdi-plus-network-outline mdi-18px">
                                                    <?= $_["install_load_balancer"] ?></a></li>
                                    <?php }
                                    if (hasPermissions("adv", "servers")) { ?>
                                        <li><a href="./servers.php"><span class="mdi mdi-server-network mdi-18px"></span>
                                                <?= $_["manage_servers"] ?></a></li>
                                    <?php }
                                    if (hasPermissions("adv", "servers")) { ?>
                                        <li><a href="./smonitor.php"><span class="mdi mdi-chart-line-variant mdi-18px"></span>
                                                <?= $_["server_monitor"] ?></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php }
                        if ((hasPermissions("adv", "add_user")) or (hasPermissions("adv", "users")) or (hasPermissions("adv", "mass_edit_users")) or (hasPermissions("adv", "mng_regusers")) or (hasPermissions("adv", "add_reguser")) or (hasPermissions("adv", "credits_log")) or (hasPermissions("adv", "client_request_log")) or (hasPermissions("adv", "reg_userlog"))) { ?>
                            <li>
                                <a href="#"> <i
                                        class="mdi mdi-account-multiple-outline mdi-18px text-primary"></i><span><?= $_["reg_users"] ?></span>
                                    <span class="arrow-right"></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <?php if (hasPermissions("adv", "add_reguser")) { ?>
                                        <li><a href="./reg_user.php"><span class="mdi mdi-account-multiple-plus-outline mdi-18px">
                                                    <?= $_["add_registered_user"] ?></a></li>
                                    <?php }
                                    if (hasPermissions("adv", "mng_regusers")) { ?>
                                        <li><a href="./reg_users.php"><span class="mdi mdi-account-multiple-outline mdi-18px">
                                                    <?= $_["manage_registered_users"] ?></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php }
                        if ((hasPermissions("adv", "add_user")) or (hasPermissions("adv", "users")) or (hasPermissions("adv", "mass_edit_users")) or (hasPermissions("adv", "mng_regusers")) or (hasPermissions("adv", "add_reguser")) or (hasPermissions("adv", "credits_log")) or (hasPermissions("adv", "client_request_log")) or (hasPermissions("adv", "reg_userlog")) or (hasPermissions("adv", "add_mag")) or (hasPermissions("adv", "manage_mag")) or (hasPermissions("adv", "add_e2")) or (hasPermissions("adv", "manage_e2")) or (hasPermissions("adv", "manage_events"))) { ?>
                            <li>
                                <a href="#"> <i class="mdi mdi-account-outline mdi-18px text-pink"></i><span><?= $_["users"] ?>
                                    </span><span class="arrow-down"></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <?php if (hasPermissions("adv", "add_user")) { ?>
                                        <li><a href="./user.php"><span class="mdi mdi-account-plus-outline mdi-18px"></span>
                                                <?= $_["add_user"] ?></a></li>
                                    <?php }
                                    if (hasPermissions("adv", "users")) { ?>
                                        <li><a href="./users.php"><span class="mdi mdi-account-multiple-outline mdi-18px">
                                                    <?= $_["manage_users"] ?></a></li>
                                    <?php }
                                    if ((hasPermissions("adv", "add_mag")) or (hasPermissions("adv", "manage_mag"))) { ?>
                                        <div class="separator"></div>
                                    <?php }
                                    if (hasPermissions("adv", "add_mag")) { ?>
                                        <li><a href="./user.php?mag"><span class="mdi mdi-account-plus-outline mdi-18px"></span>
                                                <?= $_["add_mag"] ?></a></li>
                                        <!--<li><a href="./mag.php"><?= $_["link_mag"] ?></a></li>-->
                                    <?php }
                                    if (hasPermissions("adv", "manage_mag")) { ?>
                                        <li><a href="./mags.php"><span class="mdi mdi-account-multiple-outline mdi-18px">
                                                    <?= $_["manage_mag_devices"] ?></a></li>
                                    <?php }
                                    if (hasPermissions("adv", "add_mag")) { ?>
                                        <li><a href="./mag.php"><span class="mdi mdi-account-switch mdi-18px">
                                                    <?= $_["link_mag"] ?></a></li>
                                    <?php }
                                    if ((hasPermissions("adv", "add_e2")) or (hasPermissions("adv", "manage_e2"))) { ?>
                                        <div class="separator"></div>
                                    <?php }
                                    if (hasPermissions("adv", "add_e2")) { ?>
                                        <li><a href="./user.php?e2"><span class="mdi mdi-account-plus-outline mdi-18px"></span>
                                                <?= $_["add_enigma"] ?></a></li>
                                        <!--<li><a href="./enigma.php"><?= $_["link_enigma"] ?></a></li>-->
                                    <?php }
                                    if (hasPermissions("adv", "manage_e2")) { ?>
                                        <li><a href="./enigmas.php"><span class="mdi mdi-account-multiple-outline mdi-18px">
                                                    <?= $_["manage_enigma_devices"] ?></a></li>
                                    <?php }
                                    if (hasPermissions("adv", "add_e2")) { ?>
                                        <li><a href="./enigma.php"><span class="mdi mdi-account-switch mdi-18px">
                                                    <?= $_["link_enigma"] ?></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php }
                    } else { ?>
                        <li>
                            <a href="#"> <i class="mdi mdi-account-outline mdi-18px text-pink"></i><span><?= $_["users"] ?>
                                </span><span class="arrow-down"></span></a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <?php if ((!$rAdminSettings["disable_trial"]) && ($rPermissions["total_allowed_gen_trials"] > 0) && ($rUserInfo["credits"] >= $rPermissions["minimum_trial_credits"])) { ?>
                                    <li><a href="./user_reseller.php?trial"><span
                                                class="mdi mdi-account-plus-outline mdi-18px"></span>
                                            <?= $_["generate_trial"] ?></a></li>
                                    <p>
                                    <?php } ?>
                                    <div class="separator"></div>
                                    <li><a href="./user_reseller.php"><span
                                                class="mdi mdi-account-plus-outline mdi-18px"></span> <?= $_["add_user"] ?></a>
                                    </li>
                                    <p>
                                        <li><a href="./users.php"><span class="mdi mdi-account-multiple-outline mdi-18px">
                                                    <?= $_["manage_users"] ?></a></li>
                                    <p>
                                    <div class="separator"></div>
                                    <li><a href="./user_reseller.php?mag"><span
                                                class="mdi mdi-account-plus-outline mdi-18px"></span> <?= $_["add_mag"] ?></a>
                                    </li>
                                    <p>
                                        <li><a href="./mags.php"><span class="mdi mdi-account-multiple-outline mdi-18px">
                                                    <?= $_["manage_mag_devices"] ?></a></li>
                                    <p>
                                    <div class="separator"></div>
                                    <li><a href="./user_reseller.php?e2"><span
                                                class="mdi mdi-account-plus-outline mdi-18px"></span>
                                            <?= $_["add_enigma"] ?></a></li>
                                    <p>
                                        <li><a href="./enigmas.php"><span class="mdi mdi-account-multiple-outline mdi-18px">
                                                    <?= $_["manage_enigma_devices"] ?></a></li>
                            </ul>
                        </li>
                    <?php }
                    if (($rPermissions["is_reseller"]) && ($rPermissions["create_sub_resellers"])) { ?>
                        <li>
                            <a href="#"> <i
                                    class="mdi mdi-account-multiple-outline mdi-18px text-primary"></i><span><?= $_["reg_users"] ?></span><span
                                    class="arrow-right"></span></a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <?php if ($rPermissions["is_admin"]) { ?>
                                    <li><a href="./reg_user.php"><span class="mdi mdi-account-multiple-plus-outline mdi-18px">
                                                <?= $_["add_subreseller"] ?></a></li>
                                <?php } else { ?>
                                    <li><a href="./subreseller.php"><span
                                                class="mdi mdi-account-multiple-plus-outline mdi-18px">
                                                <?= $_["add_subreseller"] ?></a></li>
                                <?php } ?>
                                <li><a href="./reg_users.php"><span class="mdi mdi-account-multiple-outline mdi-18px">
                                            <?= $_["manage_subresellers"] ?></a></li>
                            </ul>
                        </li>
                        <?php }
                    if ($rPermissions["is_admin"]) {
                        if ((hasPermissions("adv", "add_movie")) or (hasPermissions("adv", "import_movies")) or (hasPermissions("adv", "movies")) or (hasPermissions("adv", "series")) or (hasPermissions("adv", "add_series")) or (hasPermissions("adv", "radio")) or (hasPermissions("adv", "add_radio")) or (hasPermissions("adv", "mass_sedits_vod")) or (hasPermissions("adv", "mass_sedits")) or (hasPermissions("adv", "mass_edits_radio"))) { ?>
                            <li>
                                <a href="#"> <i
                                        class="mdi mdi-video-outline mdi-18px text-success"></i><span><?= $_["vod"] ?></span><span
                                        class="arrow-right"></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <?php if (hasPermissions("adv", "add_movie")) { ?>
                                        <li><a href="./movie.php"><span class="mdi mdi-plus mdi-18px"> <?= $_["add_movie"] ?></a>
                                        </li>
                                    <?php }
                                    if (hasPermissions("adv", "movies")) { ?>
                                        <li><a href="./movies.php"><span class="mdi mdi-movie mdi-18px">
                                                    <?= $_["manage_movies"] ?></a></li>
                                    <?php }
                                    if ((hasPermissions("adv", "add_series")) or (hasPermissions("adv", "series")) or (hasPermissions("adv", "episodes"))) { ?>
                                        <div class="separator"></div>
                                    <?php }
                                    if (hasPermissions("adv", "add_series")) { ?>
                                        <li><a href="./serie.php"><span class="mdi mdi-plus mdi-18px"> <?= $_["add_series"] ?></a>
                                        </li>
                                    <?php }
                                    if (hasPermissions("adv", "series")) { ?>
                                        <li><a href="./series.php"><span class="mdi mdi-youtube-tv mdi-18px">
                                                    <?= $_["manage_series"] ?></a></li>
                                    <?php }
                                    if (hasPermissions("adv", "episodes")) { ?>
                                        <li><a href="./episodes.php"><span class="mdi mdi-youtube-tv mdi-18px">
                                                    <?= $_["manage_episodes"] ?></a></li>
                                    <?php }
                                    if ((hasPermissions("adv", "mass_sedits_vod")) or (hasPermissions("adv", "mass_sedits")) or (hasPermissions("adv", "mass_edit_radio"))) { ?>
                                        <div class="separator"></div>
                                    <?php }
                                    if (hasPermissions("adv", "add_radio")) { ?>
                                        <li><a href="./radio.php"><span class="mdi mdi-plus mdi-18px"> <?= $_["add_station"] ?></a>
                                        </li>
                                    <?php }
                                    if (hasPermissions("adv", "radio")) { ?>
                                        <li><a href="./radios.php"><span class="mdi mdi-radio mdi-18px">
                                                    <?= $_["manage_stations"] ?></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php }
                        if ((hasPermissions("adv", "add_stream")) or (hasPermissions("adv", "import_streams")) or (hasPermissions("adv", "create_channel")) or (hasPermissions("adv", "streams")) or (hasPermissions("adv", "mass_edit_streams")) or (hasPermissions("adv", "stream_tools")) or (hasPermissions("adv", "stream_errors")) or (hasPermissions("adv", "fingerprint"))) { ?>
                            <li>
                                <a href="#"> <i
                                        class="mdi mdi-play-circle-outline mdi-18px text-info"></i><span><?= $_["streams"] ?></span><span
                                        class="arrow-right"></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <?php if (hasPermissions("adv", "add_stream")) { ?>
                                        <li><a href="./stream.php"><span class="mdi mdi-plus mdi-18px"> <?= $_["add_stream"] ?></a>
                                        </li>
                                    <?php }
                                    if (hasPermissions("adv", "streams")) { ?>
                                        <li><a href="./streams.php"><span class="mdi mdi-play-circle-outline mdi-18px">
                                                    <?= $_["manage_streams"] ?></a></li>
                                    <?php }
                                    if (hasPermissions("adv", "create_channel")) { ?>
                                        <li><a href="./created_channel.php"><span class="mdi mdi-plus mdi-18px">
                                                    <?= $_["create_channel"] ?></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php }
                        if ((hasPermissions("adv", "add_bouquet")) or (hasPermissions("adv", "bouquets"))) { ?>
                            <li>
                                <a href="#"> <i
                                        class="mdi mdi-flower-tulip-outline text-purple"></i><span><?= $_["bouquets"] ?></span><span
                                        class="arrow-right"></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <?php if (hasPermissions("adv", "add_bouquet")) { ?>
                                        <li><a href="./bouquet.php"><span class="mdi mdi-plus mdi-18px">
                                                    <?= $_["add_bouquet"] ?></a></li>
                                    <?php }
                                    if (hasPermissions("adv", "bouquets")) { ?>
                                        <li><a href="./bouquets.php"><span class="mdi mdi-flower-tulip-outline mdi-18px">
                                                    <?= $_["manage_bouquets"] ?></a></li>
                                    <?php }
                                    if (hasPermissions("adv", "edit_bouquet")) { ?>
                                        <li><a href="./bouquet_sort.php"><span class="mdi mdi-reorder-horizontal mdi-18px">
                                                    <?= $_["order_bouquets"] ?></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php }
                    }
                    if (($rPermissions["is_reseller"]) && ($rPermissions["reset_stb_data"])) { ?>
                        <li>
                            <a href="#"> <i
                                    class="mdi mdi-play-circle-outline mdi-18px text-info"></i><span><?= $_["content"] ?></span><span
                                    class="arrow-right"></span></a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="./streams.php"><?= $_["streams"] ?></a></li>
                                <li><a href="./movies.php"><?= $_["movies"] ?></a></li>
                                <li><a href="./series.php"><?= $_["series"] ?></a></li>
                                <li><a href="./episodes.php"><?= $_["episodes"] ?></a></li>
                                <li><a href="./radios.php"><?= $_["stations"] ?></a></li>
                            </ul>
                        </li>
                    <?php }
                    if ((hasPermissions("adv", "add_user")) or (hasPermissions("adv", "users")) or (hasPermissions("adv", "mass_edit_users")) or (hasPermissions("adv", "mng_regusers")) or (hasPermissions("adv", "add_reguser")) or (hasPermissions("adv", "credits_log")) or (hasPermissions("adv", "panel_errors")) or (hasPermissions("adv", "client_request_log")) or (hasPermissions("adv", "reg_userlog")) or (hasPermissions("adv", "live_connections")) or (hasPermissions("adv", "connection_logs")) or (hasPermissions("adv", "stream_errors")) or (hasPermissions("adv", "manage_events")) or (hasPermissions("adv", "system_logs"))) { ?>
                        <li>
                            <a href="#"> <i
                                    class="mdi mdi-information-outline mdi-18px text-danger"></i><span><?= $_["logs"] ?>
                                </span><span class="arrow-rigth"></span></a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <?php if (hasPermissions("adv", "live_connections")) { ?>
                                    <li><a href="./live_connections.php"><span class="mdi mdi-account-network-outline mdi-18px">
                                                <?= $_["live_connections"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "connection_logs")) { ?>
                                    <li><a href="./user_activity.php"><span class="mdi mdi-file-document-outline mdi-18px">
                                                <?= $_["activity_logs"] ?></a></li>
                                    <li><a href="./user_ips.php"><span class="mdi mdi-ip mdi-18px">
                                                <?= $_["line_ip_usage"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "panel_errors")) { ?>
                                    <li><a href="./panel_logs.php"><span class="mdi mdi-file-document-outline mdi-18px">
                                                <?= $_["panel_logs"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "system_logs")) { ?>
                                    <li><a href="./system_logs.php"><span class="mdi mdi-file-document-outline mdi-18px">
                                                <?= $_["system_logs"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "credits_log")) { ?>
                                    <li><a href="./credit_logs.php"><span class="mdi mdi-credit-card-multiple mdi-18px">
                                                <?= $_["credit_logs"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "client_request_log")) { ?>
                                    <li><a href="./client_logs.php"><span class="mdi mdi-account-search mdi-18px">
                                                <?= $_["client_logs"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "reg_userlog")) { ?>
                                    <li><a href="./reg_user_logs.php"><span class="mdi mdi-account-details mdi-18px">
                                                <?= $_["reseller_logs"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "stream_errors")) { ?>
                                    <li><a href="./stream_logs.php"><span class="mdi mdi-file-document-outline mdi-18px">
                                                <?= $_["stream_logs"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "manage_events")) { ?>
                                    <li><a href="./mag_events.php"><span class="mdi mdi-message-outline mdi-18px">
                                                <?= $_["mag_event_logs"] ?></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php }
                    if ((hasPermissions("adv", "add_user")) or (hasPermissions("adv", "users")) or (hasPermissions("adv", "mass_edit_users")) or (hasPermissions("adv", "import_streams")) or (hasPermissions("adv", "streams")) or (hasPermissions("adv", "mass_edit_streams")) or (hasPermissions("adv", "manage_events")) or (hasPermissions("adv", "import_movies")) or (hasPermissions("adv", "movies")) or (hasPermissions("adv", "series")) or (hasPermissions("adv", "radio")) or (hasPermissions("adv", "mass_sedits_vod")) or (hasPermissions("adv", "mass_sedits")) or (hasPermissions("adv", "mass_edits_radio")) or (hasPermissions("adv", "stream_tools")) or (hasPermissions("adv", "fingerprint")) or (hasPermissions("adv", "mass_delete"))) { ?>
                        <li>
                            <a href="#"> <i
                                    class="mdi mdi-progress-wrench mdi-18px text-primary"></i><span><?= $_["tools"] ?></span><span
                                    class="arrow-right"></span></a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <?php if (hasPermissions("adv", "mass_edit_users")) { ?>
                                    <li><a href="./user_mass.php"><span class="mdi mdi-account-edit mdi-18px">
                                                <?= $_["mass_edit_users"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "mass_edit_streams")) { ?>
                                    <li><a href="./stream_mass.php"><span class="mdi mdi-border-color mdi-18px">
                                                <?= $_["mass_edit_streams"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "mass_sedits_vod")) { ?>
                                    <li><a href="./movie_mass.php"><span class="mdi mdi-border-color mdi-18px">
                                                <?= $_["mass_edit_movies"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "mass_sedits")) { ?>
                                    <li><a href="./series_mass.php"><span class="mdi mdi-border-color mdi-18px">
                                                <?= $_["mass_edit_series"] ?></a></li>
                                    <li><a href="./episodes_mass.php"><span class="mdi mdi-border-color mdi-18px">
                                                <?= $_["mass_edit_episodes"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "mass_edit_radio")) { ?>
                                    <li><a href="./radio_mass.php"><span class="mdi mdi-border-color mdi-18px">
                                                <?= $_["mass_edit_stations"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "mass_delete")) { ?>
                                    <li><a href="./mass_delete.php"><span class="mdi mdi-delete-outline mdi-18px">
                                                <?= $_["mass_delete"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "fingerprint")) { ?>
                                    <li><a href="./fingerprint.php"><span class="mdi mdi-fingerprint mdi-18px">
                                                <?= $_["fingerprint"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "stream_tools")) { ?>
                                    <li><a href="./stream_tools.php"><span
                                                class="mdi mdi-wrench-outline mdi-rotate-90 mdi-18px">
                                                <?= $_["stream_tools"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "import_movies")) { ?>
                                    <li><a href="./movie.php?import"><span class="mdi mdi-file-plus mdi-18px">
                                                <?= $_["import_movies"] ?></a></li>
                                <?php }
                                if (hasPermissions("adv", "import_streams")) { ?>
                                    <li><a href="./stream.php?import"><span class="mdi mdi-file-plus mdi-18px">
                                                <?= $_["import_streams"] ?></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php }
                    if ($rPermissions["is_reseller"]) { ?>
                        <li>
                            <a href="#"> <i
                                    class="mdi mdi-email-outline mdi-18px text-warning"></i><span><?= $_["support"] ?></span><span
                                    class="arrow-right"></span></a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="./ticket.php"><span class="mdi mdi-message-text-outline mdi-18px">
                                            <?= $_["create_ticket"] ?></a></li>
                                <li><a href="./tickets.php"><span class="mdi mdi-message-settings-variant mdi-18px">
                                            <?= $_["manage_tickets"] ?></a></li>
                                <?php if ($rPermissions["allow_import"]) { ?>
                                    <li><a href="./resellersmarters.php"><span
                                                class="mdi mdi-message-settings-variant mdi-18px"> Reseller API Key</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <li>
                            <a href="#"> <i class="mdi mdi-apps mdi-18px text-success"></i><span>Apps Iptv </span><span
                                    class="arrow-right"></span></a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="./duplexplay.php"><span class="mdi mdi-account-star mdi-18px"> DUPLEX IPTV</a>
                                </li>
                                <li><a href="./netiptv.php"><span class="mdi mdi-account-star mdi-18px"> NET IPTV</a></li>
                                <li><a href="./siptv.php"><span class="mdi mdi-account-star mdi-18px"> SMART IPTV</a></li>
                                <li><a href="./siptvextreme.php"><span class="mdi mdi-account-star mdi-18px"> IPTV
                                            EXTREME</a></li>
                                <li><a href="./nanomid.php"><span class="mdi mdi-account-star mdi-18px"> NANOMID</a></li>
                                <!--<li><a href="./ss-iptv.php"><span class=mdi mdi-account-star mdi-18px"> SS IPTV</a></li>-->
                            </ul>
                        </li>
                    <?php }
                    if (($rPermissions["is_admin"]) && (hasPermissions("adv", "manage_tickets"))) { ?>
                        <li>
                            <a href="./tickets.php"> <i
                                    class="mdi mdi-email-outline mdi-18px text-pink"></i><span><?= $_["tickets"] ?></span></a>
                        </li>
                        <li>
                            <a href="#"> <i class="mdi mdi-apps mdi-18px text-success"></i><span>Apps Iptv </span><span
                                    class="arrow-right"></span></a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="./duplexplay.php"><span class="mdi mdi-account-star mdi-18px"> DUPLEX IPTV</a>
                                </li>
                                <li><a href="./netiptv.php"><span class="mdi mdi-account-star mdi-18px"> NET IPTV</a></li>
                                <li><a href="./siptv.php"><span class="mdi mdi-account-star mdi-18px"> SMART IPTV</a></li>
                                <li><a href="./siptvextreme.php"><span class="mdi mdi-account-star mdi-18px"> IPTV
                                            EXTREME</a></li>
                                <li><a href="./nanomid.php"><span class="mdi mdi-account-star mdi-18px"> NANOMID</a></li>
                                <!--<li><a href="./ss-iptv.php"><span class="mdi mdi-account-star mdi-18px"> SS IPTV</a></li>-->
                            </ul>
                        </li>
                    <?php }
                    if (($rPermissions["is_reseller"]) && ($rAdminSettings["active_mannuals"])) { ?>
                        <li>
                            <a href="./reseller_mannuals.php"> <i
                                    class="mdi mdi-book-open-page-variant mdi-18px text-info"></i><span><?= $_["mannuals"] ?></span></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <!-- End Sidebar -->
            <div class="clearfix"></div>
        </div>
        <!-- Sidebar -left -->
    </div>