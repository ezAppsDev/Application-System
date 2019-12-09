<?php
session_name('ezApps');
session_start();
require 'tyler_base/global/connect.php';
require 'tyler_base/global/config.php';
$page['name'] = 'Thank You';

if (!loggedIn) {
    header('Location: '.DOMAIN.'/login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require 'tyler_base/page/header.php'; ?>
</head>

<body>
    <?php require 'tyler_base/page/nav.php'; ?>
    <?php require 'tyler_base/page/s-nav.php'; ?>
    <div class="lime-container">
        <div class="lime-body">
            <div class="container">
            <div id="ezaMsg"><?php if (isset($message)) { echo $message; } ?></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="dashboard-info row">
                                    <div class="info-text col-md-6">
                                        <h5 class="card-title">Development Credits</h5>
                                        <ul>
                                            <li>Project Manager - Tyler#7918</li>
                                            <hr>
                                            <li>DONATORS</li>
                                            <li>Indy Joy W.#9888</li>
                                            <li>Saiys#6795</li>
                                            <li>DXNCXX#1056</li>
                                            <li>kingdobby#1234</li>
                                            <li>Rhys19#0592</li>
                                            <li>ADRNALN#0001</li>
                                            <li>JT x 412#3956</li>
                                            <li>MrCalibooso#3329</li>
                                            <li>Mxrcy#0001</li>
                                            <li>WaveShredder#6349</li>
                                            <li>BreakfastDeliqht 🎄#0801</li>
                                            <li>OnyxEye-RocketCityGamerz🚀#7978</li>
                                            <li>Jake Bishep#0001</li>
                                            <li>NotCamSlice#6517</li>
                                            <li>Dracuslayer#1992</li>
                                            <li>PattoeO26#6969</li>
                                            <li>Geral#0534</li>
                                            <li>Ponch#3784</li>
                                            <li>Green#8177</li>
                                            <li>Jorrit#0001</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require 'tyler_base/page/copyright.php'; ?>
    </div>

    <?php require 'tyler_base/page/footer.php'; ?>
</body>

</html>