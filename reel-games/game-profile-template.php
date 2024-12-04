<?php
/**
 * Template for displaying game profile based on the dynamic URL. 
 */

$game_data = fetch_reel_game_api_response();

if (is_wp_error($game_data)) {
    echo esc_html($game_data->get_error_message());
    return;
}

if (!isset($game_data['title'])) {
    echo "Game data not available.";
    return;
}

//page container
echo "<div class='reel-game-page-container'>";

    echo "<div class='alert alert-warning alert-dismissible fade show m-5' role='alert'>";
        echo "This is a limited preview of the full Game Profile page of <b>" . esc_html($game_data['title']) . "</b> by <i>" . esc_html($game_data['supplier']) ."</i>.";
    echo "</div>";


    // 1. Game Info & Metrics
    echo "<div class='reel-game-container'>";
        echo "<div class='main-flex-container'>";
            echo "<div class='game-info'>";
                echo "<h1 class='reel-game-title'>" . esc_html($game_data['title']) . "</h1>";
                echo "<ul><li><strong>Supplier: </strong>" . esc_html($game_data['supplier']) . "</li>";
                echo "<li><strong>Cost Model: </strong>" . esc_html($game_data['cost_model']) . "</ul>";
            echo "</div>"; 
                echo "<div class='metrics-flex-container'>";
                    $metrics_titles = ["TERMINALS", "VENUES", "MONTHS"];
                    foreach ($metrics_titles as $index => $title) {
                        echo "<div class='metric-box'>";
                        echo "<h6>$title</h6>";
                        echo "<div class='metric-number'>" . esc_html($game_data['stats_json'][$index][0]['value']) . "</div>";
                        $skip_first = true;
                        echo "<table>";
                        foreach ($game_data['stats_json'][$index] as $metric) {
                            if ($skip_first) {
                                $skip_first = false;
                                continue;
                            }
                                // Check if the key is 'Retired' and the value is 0, then skip this iteration
                                if ($metric['key'] === 'Retired') {
                                    continue;
                                }
                            echo "<tr>";
                            echo "<td class='metric-table-cell'><strong>" . esc_html($metric['key']) . ":</strong></td>";
                            echo "<td class='metric-table-cell metric-cell-value'>" . esc_html($metric['value']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        
                        echo "</div>";
                    }
                    echo "<hr class='mt-4 mb-5'>";
                echo "</div>"; // Close the metrics-flex-container
        echo "</div>"; // Close the main-flex-container

        // Game Image and Performance Profile

        // echo "<div class='container'>";
        // echo "<div class='row'>";
        // echo "<div class='col-md-5'>";
        // echo "<h5>Game Image</h5>";
        // echo "</div>";
        // echo "<div class='col-md-7 mt-5'>";
        // echo "<table class='table table-bordered'>";
        // echo "<thead><tr><th>Measure</th><th>Value</th></tr></thead><tbody>";
        // foreach ($game_data['performance_profile_json'] as $measure => $value) {
        //     echo "<tr><td>" . esc_html($measure) . "</td><td>" . esc_html($value) . "</td></tr>";
        // }
        // echo "</tbody></table>";
        // echo "</div>";
        // echo "</div>";
        // echo "</div>";
    echo "</div>"; // Close the reel-game-container

    echo "<div class='reel-locked-container'>";
        echo "<div class='locked-card-header'>";
            echo "<h4 class='locked-card-title'>Configuration Details <i class='fa fa-lock lock-icon'></i></h4>";
            echo "<a href='https://reelmetrics.com' target='_blank'><button class='btn orange-btn'>Learn More  <i class='fa fa-external-link'></i></button></a>"; 
        echo "</div>";
    echo "</div>";

    echo "<div class='reel-locked-container'>";
        echo "<div class='locked-card-header'>";
            echo "<h4 class='locked-card-title'>Player Data <i class='fa fa-lock lock-icon'></i></h4>";   
            echo "<a href='https://reelmetrics.com' target='_blank'><button class='btn blue-btn'>Learn More  <i class='fa fa-external-link'></i></button></a>"; 
        echo "</div>";
    echo "</div>";





// start of tesst code
$accordionGroupId = "accordionGroup";

$internal_overview_url = plugins_url( 'assets/img/internal_overview.png', __FILE__);
$pricing_url = plugins_url( 'assets/img/pricing.png', __FILE__);
$average_bet_url = plugins_url( 'assets/img/average_bet.png', __FILE__);
$conversions_url = plugins_url( 'assets/img/conversions.png', __FILE__);
$loco_url = plugins_url( 'assets/img/loco.png', __FILE__);


// Vertical Box Row
    echo "<div class='vertical-chart-container mt-3'>";
        echo "<div class='chart-container'>";

        echo "<div class='chart-box chart-box-locked' data-toggle='collapse' data-target='#collapseContactCTA' aria-expanded='false' aria-controls='collapseContactCTA'>";
        echo "<div class='popup-card-header'>";
            echo "<h4 class='locked-card-title-small-box'> Internal Overview </h4> ";
        echo "</div>";
        echo "<div class='popup-card-body'>";
            echo "<div class='backdrop-image' style='background-image: url(" . esc_url( $internal_overview_url ) . ");'>";
                echo "<button type='button' class='center-btn' data-toggle='collapse' data-target='#popup1'>Click to Learn More</button>";
            echo "</div>"; // Close backdrop-image div
        echo "</div>";  // Close the popup-card-body
    echo "</div>"; // Close the first chart-box
    
        // Contact Box for CTA
        echo "<div class='chart-box chart-box-locked' data-toggle='collapse' data-target='#collapseContactCTA' aria-expanded='false' aria-controls='collapseContactCTA'>";
            echo "<div class='popup-card-header'>";
                echo "<h4 class='locked-card-title-small-box'>Pricing Analysis</h4>";
            echo "</div>";
            echo "<div class='popup-card-body'>";
            echo "<div class='backdrop-image' style='background-image: url(" . esc_url( $pricing_url ) . ");'>";
                echo "<button type='button' class='center-btn' data-toggle='collapse' data-target='#popup1'>Click to Learn More</button>";
            echo "</div>"; // Close backdrop-image div
        echo "</div>";  // Close the popup-card-body
    echo "</div>"; // Close the first chart-box


            //Time Series Box
            echo "<div class='chart-box' data-toggle='collapse' data-target='#collapseGamePerformance' aria-expanded='false' aria-controls='collapseGamePerformance'>";
                echo "<h4 class='popup-card-title'>Time Series Analysis</h4>";
                echo "<div id='timeSeriesChartPreview'></div>";
            echo "</div>";

            //Performance Profile Box
            echo "<div class='chart-box' data-toggle='collapse' data-target='#collapsePerformanceProfile' aria-expanded='false' aria-controls='collapsePerformanceProfile'>";
                echo "<div class='performance-profile-header'>";
                    echo "<h4 class='popup-card-title'>Performance Profile</h4>";
                            echo "<div class='metrics-overview'>";
                                echo "<table class='table'>";
                                    echo "<thead><tr><th>Measure</th><th>Value</th></tr></thead><tbody>";
                                    foreach ($game_data['performance_profile_json'] as $measure => $value) {
                                        echo "<tr><td>" . esc_html($measure) . "</td><td>" . esc_html($value) . "</td></tr>";
                                    }
                                echo "</tbody></table>";
                            echo "</div>";
                            echo "<span class='chevron'><i class='fa fa-chevron-down'></i></span>";
                echo "</div>"; // Close the box
            echo "</div>"; // Close the Performance Profile Box

        echo "</div>"; // Close the chart-container
    echo "</div>"; // Close the vertical-chart-container


    // PopUp content for Performance Profile
    echo "<div id='{$accordionGroupId}' class='accordion'>";
        echo "<div class='popUpBox'>";
            echo "<div id='collapsePerformanceProfile' class='collapse' aria-labelledby='headingPerformanceProfile' data-parent='#{$accordionGroupId}'>";
                echo "<div class='card-body'>";
                
                        echo "<h4>Key Metrics</h4>";
                        echo "<table class='table'>";
                            echo "<thead><tr><th>Measure</th><th>Value</th></tr></thead><tbody>";
                            foreach ($game_data['performance_profile_json'] as $measure => $value) {
                                echo "<tr><td>" . esc_html($measure) . "</td><td>" . esc_html($value) . "</td></tr>";
                            }
                        echo "</tbody></table>";

                        echo "<div class='vertical-chart-box'>";
                        echo "<h5>Games Played & Theo Win Blend Percentile</h5>";
                        echo "<div id='gamesPlayedTheoWinBlendPercentile'></div>"; 
                    echo "</div>";

                    echo "<div class='vertical-chart-box'>";
                        echo "<h5>Theo Win Percentile</h5>";
                        echo "<div id='theoWinPercentile'></div>";
                    echo "</div>";
                    
                    echo "<div class='vertical-chart-box'>";
                        echo "<h5>Games Played Percentile</h5>";
                        echo "<div id='gamesPlayedPercentile'></div>";
                    echo "</div>";
                    
                echo "</div>"; // Close the card-body 
            echo "</div>"; // Collapse
        echo "</div>"; // Close the performance-profile-container
    echo "</div>"; // Close the accordion

    // PopUp content for TimeSeriesAnalysis
    echo "<div id='{$accordionGroupId}' class='accordion'>";
        echo "<div class='popUpBox'>";
            echo "<div id='collapseGamePerformance' class='collapse' aria-labelledby='headingGamePerformance' data-parent='#{$accordionGroupId}'>";
                echo "<div class='card-body'>";
                    echo "<h4>Time Series Analysis</h4>";
                    echo "<p>The Time Series Analysis shows performance (Blended Games Played Theo Win vs Floor Average) over time.</p>";


                    // TimeSeries Chart
                    echo "<div class='vertical-chart-box'>";
                        echo "<div id='timeSeriesChart'></div>"; //Performance over Time
                    echo "</div>";

                    echo "<div class='vertical-chart-box'>";
                        echo "<h5>Terminals</h5>";
                        echo "<div id='terminalsBarChart'></div>"; //terminaals
                    echo "</div>";

                    echo "<div class='vertical-chart-box'>";
                        echo "<h5>Market Share</h5>";
                        echo "<div id='marketShareBarChart'></div>";  //market share
                    echo "</div>";
                    
                    echo "<div class='vertical-chart-box'>";
                        echo "<h5>Venues</h5>";
                        echo "<div id='venuesBarChart'></div>";  //venues 
                    echo "</div>";

                echo "</div>"; // Close the card-body
            echo "</div>"; // Close the Accordion content
        echo "</div>"; // Close the chart-container
    echo "</div>"; // Close the accordion

    // PopUp content for Contact CTA
    echo "<div id='{$accordionGroupId}' class='accordion'>";
        echo "<div class='popUpBox'>";
            echo "<div id='collapseContactCTA' class='collapse' aria-labelledby='headingContactCTA' data-parent='#{$accordionGroupId}'>";
                echo "<div class='card-body'>";
                    echo "<h4>Premium Feature</h4>";
                    echo "<p>Like what you've seen so far? Then you're going to LOVE what's waiting for you inside.";

                    echo "<p>
                    ReelMetrics subscriptions provide you with unfettered access to the world's largest slot repository and our full suite of groundbreaking advisory apps. Affordably priced and packed with value, ReelMetrics subscriptions deliver robust returns on annual subscription fees, a claim that you can verify with existing ReelMetrics Subscribers.
                    </p>";

                    echo "<p> To learn more about ReelMetrics subscriptions, <a href='mailto:info@reelmetrics.com'> give us a yodel</a> and learn how our Big Data solutions help you take the guesswork out of game work.</p>";
                    echo "<button type='button' class='btn btn-secondary' data-toggle='collapse' data-target='#collapseContactCTA' aria-expanded='false' aria-controls='collapseContactCTA'>Close</button>";
                echo "</div>"; // Close the card-body
            echo "</div>"; // Close the Accordion content
        echo "</div>"; // Close the chart-container
    echo "</div>"; // Close the accordion



    // Vertical Box Row
    echo "<div class='vertical-chart-container mt-5'>";
        echo "<div class='chart-container'>";

            echo "<div class='chart-box chart-box-locked' data-toggle='collapse' data-target='#collapseContactCTA' aria-expanded='false' aria-controls='collapseContactCTA'>";
                echo "<div class='popup-card-header'>";
                    echo "<h4 class='locked-card-title-small-box'>Locational Analysis</h4>";
                echo "</div>";
                echo "<div class='popup-card-body'>";
                    echo "<div class='backdrop-image' style='background-image: url(" . esc_url( $loco_url ) . ");'>";
                        echo "<button type='button' class='center-btn' data-toggle='collapse' data-target='#popup1'>Click to Learn More</button>";
                    echo "</div>"; // Close backdrop-image div
                echo "</div>";  // Close the popup-card-body
            echo "</div>"; // Close the first chart-box

            echo "<div class='chart-box chart-box-locked' data-toggle='collapse' data-target='#collapseContactCTA' aria-expanded='false' aria-controls='collapseContactCTA'>";
                echo "<div class='popup-card-header'>";
                    echo "<h4 class='locked-card-title-small-box'>Game Conversions</h4>"; 
                echo "</div>";
                echo "<div class='popup-card-body'>";
                    echo "<div class='backdrop-image' style='background-image: url(" . esc_url( $conversions_url ) . ");'>";
                        echo "<button type='button' class='center-btn' data-toggle='collapse' data-target='#popup1'>Click to Learn More</button>";
                    echo "</div>"; // Close backdrop-image div
                echo "</div>";  // Close the popup-card-body
            echo "</div>"; // Close the second chart-box

            echo "<div class='chart-box chart-box-locked' data-toggle='collapse' data-target='#collapseContactCTA' aria-expanded='false' aria-controls='collapseContactCTA'>";
                echo "<div class='popup-card-header'>";
                    echo "<h4 class='locked-card-title-small-box'>Average Bet</h4>"; 
                echo "</div>";
                echo "<div class='popup-card-body'>";
                    echo "<div class='backdrop-image' style='background-image: url(" . esc_url( $average_bet_url ) . ");'>";
                        echo "<button type='button' class='center-btn' data-toggle='collapse' data-target='#popup1'>Click to Learn More</button>";
                    echo "</div>"; // Close backdrop-image div
                echo "</div>";  // Close the popup-card-body
            echo "</div>"; // Close the third chart-box

        echo "</div>"; // Close the chart-container
    echo "</div>"; // Close the vertical-chart-container

echo "</div>"; // Close the reel-game-page-container