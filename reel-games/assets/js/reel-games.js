jQuery(document).ready(function($) {

    $('body').addClass('dx-viewport');

    // Color Palette
    const mainColor = '#b91c1a';
    const darkBlueColor = '#1976D2';
    const lightBlueColor = '#00bfff';
    const darkGreyColor = '#323232';
    const greenColor = '#17A398';
    const orangeColor = '#F57C00';
    const whiteColor = '#ffffff';

    const chartHeight = 150;

    // Extract and transform the data for each metric . reelGamesData is a global variable set  in reel-games.php
    const consolidatedData = reelGamesData.gameData.lifecyle_analysis_json.map(item => {
        return {
            monthYear: new Date(item.reporting_month_yyyymm).toLocaleString('default', { month: 'short', year: 'numeric' }),
            terminals: item.terminals,
            marketShare: item.marketshare,
            venues: item.venues
        };

    });

        // 2. TimeSeries Chart initialization Performance over Time
    if ($('#timeSeriesChart').length) {
            const timeSeriesData = reelGamesData.gameData.lifecyle_analysis_json.map(item => {
                return {
                    date: new Date(item.reporting_month_yyyymm),
                    gptwRpm: item.gptw_rpm,
                    floorAverage: 1, // Assuming floorAverage is a constant value of 1
                    theoWin: item.tw_rpm,
                    gamesPlayed: item.gp_rpm
                };
            });
        
            $("#timeSeriesChart").dxChart({
                dataSource: timeSeriesData,
                commonSeriesSettings: {
                    argumentField: 'date',
                    type: 'spline'
                },
                palette: 'Bright',
                series: [
                    { valueField: 'gptwRpm', name: 'GPTW RPM', color: mainColor },
                    { valueField: 'floorAverage', name: 'Floor Average', color: darkGreyColor, point: { visible: false }, hoverMode: 'none',  },
                    { valueField: 'theoWin', name: 'Theo Win', color: orangeColor },
                    { valueField: 'gamesPlayed', name: 'Games Played', color: darkBlueColor }
                ],
                argumentAxis: {
                    valueMarginsEnabled: false,
                    discreteAxisDivisionMode: 'crossLabels',
                    grid: {
                        visible: false
                    }
                },
                valueAxis: [
                    {
                        name: 'gptwValueAxis',
                        position: 'left'
                    },
                    {
                        name: 'floorAverageValueAxis',
                        position: 'right'
                    }
                ],
                tooltip: {
                    enabled: true,
                    shared: false,
                    customizeTooltip: function (arg) {
                        var date = new Date(arg.argument);
                        var monthYear = date.toLocaleString('default', { month: 'long', year: 'numeric' });
                
                        return {
                            text: arg.seriesName + ': ' + '<b>' + arg.valueText + '</b>' + '<br/> <i>' + monthYear + '</i>'
                        };
                    }
                },
                
                legend: {
                    verticalAlignment: "bottom",
                    horizontalAlignment: "center"
                },
                title: "",
            });
    }

    // TimeSeries Chart Duplicate initialization
    if ($('#timeSeriesChartPreview').length) {
        const timeSeriesData = reelGamesData.gameData.lifecyle_analysis_json.map(item => {
            return {
                date: new Date(item.reporting_month_yyyymm),
                gptwRpm: item.gptw_rpm,
                floorAverage: 1, // Assuming floorAverage is a constant value of 1
                theoWin: item.tw_rpm,
                gamesPlayed: item.gp_rpm
            };
        });
    
        $("#timeSeriesChartPreview").dxChart({
            dataSource: timeSeriesData,
            commonSeriesSettings: {
                argumentField: 'date',
                type: 'spline'
            },
            series: [
                { valueField: 'gptwRpm', name: 'GPTW RPM', color: mainColor },
                { valueField: 'floorAverage', name: 'Floor Average', color: darkGreyColor, point: { visible: false }, hoverMode: 'none',  },
                { valueField: 'theoWin', name: 'Theo Win', color: orangeColor },
                { valueField: 'gamesPlayed', name: 'Games Played', color: darkBlueColor }
            ],
            argumentAxis: {
                valueMarginsEnabled: false,
                discreteAxisDivisionMode: 'crossLabels',
                grid: {
                    visible: true
                }
            },
            valueAxis: [
                {
                    name: 'gptwValueAxis',
                    position: 'left'
                },
                {
                    name: 'floorAverageValueAxis',
                    position: 'right'
                }
            ],
            tooltip: {
                enabled: false,
                shared: true
            },
            legend: {
                verticalAlignment: "top",
                horizontalAlignment: "center"
            },
        });
}

    // Terminals Bar Chart
    if ($('#terminalsBarChart').length) {
        $("#terminalsBarChart").dxChart({
            dataSource: consolidatedData,
            series: {
                argumentField: 'monthYear',
                valueField: 'terminals',
                name: 'Terminals',
                type: 'bar',
                color: mainColor, // Use the main color

            },
            legend: {
                position: "outside",
                horizontalAlignment: "right",
                verticalAlignment: "center"
            },
            size: {
                height: chartHeight
            }
        });
    }

    // Venues Bar Chart
    if ($('#venuesBarChart').length) {
        $("#venuesBarChart").dxChart({
            dataSource: consolidatedData,
            series: {
                argumentField: 'monthYear',
                valueField: 'venues',
                name: 'Venues',
                type: 'bar',
                color: darkBlueColor,
            },
            legend: {
                position: "outside",
                horizontalAlignment: "right",
                verticalAlignment: "middle"
            },
            size: {
                height: chartHeight
            }
        });
    }

    // Market Share Bar Chart
    if ($('#marketShareBarChart').length) {
        $("#marketShareBarChart").dxChart({
            dataSource: consolidatedData,
            series: {
                argumentField: 'monthYear',
                valueField: 'marketShare',
                name: 'Market Share',
                type: 'bar',
                color: orangeColor, // Use the gold color
            },
            legend: {
                position: "outside",
                horizontalAlignment: "right",
                verticalAlignment: "center"
            },
            size: {
                height: chartHeight
            }
        });
    }

    //gamesPlayedTheoWinBlendPercentile
    if ($('#gamesPlayedTheoWinBlendPercentile').length) {
        const percentileData = reelGamesData.gameData.lifecyle_analysis_json.map(item => {
            return {
                date: new Date(item.reporting_month_yyyymm),
                gpMedian: item.gp_median,
                gpLowerBound: item.gp_q1,
                gpUpperBound: item.gp_q3,
                twMedian: item.tw_median,
                twLowerBound: item.tw_q1,
                twUpperBound: item.tw_q3
            };
        });
    
        $("#gamesPlayedTheoWinBlendPercentile").dxChart({
            dataSource: percentileData,
            commonSeriesSettings: {
                argumentField: 'date',
                type: 'line'
            },
            series: [
                {
                    valueField: 'gpMedian',
                    name: 'Games Played Median',
                    color: orangeColor,
                    type: 'line'
                },
                {
                    rangeValue1Field: 'gpLowerBound',
                    rangeValue2Field: 'gpUpperBound',
                    name: 'Games Played 90% Confidence Interval',
                    color: orangeColor,
                    type: 'rangeArea'
                },
                {
                    valueField: 'twMedian',
                    name: 'Theo Win Median',
                    color: darkBlueColor,
                    type: 'line'
                },
                {
                    rangeValue1Field: 'twLowerBound',
                    rangeValue2Field: 'twUpperBound',
                    name: 'Theo Win 90% Confidence Interval',
                    color: lightBlueColor,
                    type: 'rangeArea'
                }
            ],
            valueAxis: {
                title: {
                    text: 'Value'
                },
                // Additional valueAxis settings can be added here
            },
            argumentAxis: {
                type: 'continuous',
                argumentType: 'datetime',
                grid: {
                    visible: true
                },
                label: {
                    format: 'monthAndYear'
                },
                // Configure tick interval if needed for better scale on the x-axis
            },
            tooltip: {
                enabled: true,
                customizeTooltip: function(arg) {
                    let text = arg.seriesName + ': ' + '<b>' + arg.valueText + '</b>';
                    if(arg.seriesType === 'rangeArea') {
                        text += '<br/>90% Confidence Interval: ' + '<b>' + arg.rangeValue1Text + ' - ' + arg.rangeValue2Text + '</b>';
                    }
                    return { text: text };
                }
            },
            legend: {
                verticalAlignment: 'bottom',
                horizontalAlignment: 'center'
            },
            title: {
                text: '',
            },
            // Add other configurations if necessary
        });
    }
    
    
    // theoWinPercentile
    if ($('#theoWinPercentile').length) {
        const theoWinData = reelGamesData.gameData.lifecyle_analysis_json.map(item => {
            return {
                date: new Date(item.reporting_month_yyyymm),
                median: item.tw_median,
                lowerBound: item.tw_q1,
                upperBound: item.tw_q3
            };
        });

        $("#theoWinPercentile").dxChart({
            dataSource: theoWinData,
            commonSeriesSettings: {
                argumentField: 'date',
                type: 'line'
            },
            series: [
                {
                    valueField: 'median',
                    name: 'Theo Win Median',
                    color: darkBlueColor, 
                    type: 'line'
                },
                {
                    rangeValue1Field: 'lowerBound',
                    rangeValue2Field: 'upperBound',
                    name: 'Theo Win 90% Confidence Interval',
                    color: lightBlueColor, 
                    type: 'rangeArea'
                }
            ],
            valueAxis: {
                title: {
                    text: 'Theo Win Percentile'
                },
            },
            argumentAxis: {
                type: 'continuous',
                argumentType: 'datetime',
                grid: {
                    visible: true
                },
                label: {
                    format: 'monthAndYear'
                },
            },
            tooltip: {
                enabled: true,
                customizeTooltip: function(arg) {
                    let text = arg.seriesName + ': ' + '<b>' + arg.valueText + '</b>';
                    if(arg.seriesType === 'rangeArea') {
                        text += '<br/>90% Confidence Interval: ' + '<b>' + arg.rangeValue1Text + ' - ' + arg.rangeValue2Text + '</b>';
                    }
                    return { text: text };
                }
            },
            legend: {
                verticalAlignment: 'bottom',
                horizontalAlignment: 'center'
            },
            title: {
                text: '',
            },
        });
    }



    // gamesPlayedPercentile
    if ($('#gamesPlayedPercentile').length) {
        const gamesPlayedData = reelGamesData.gameData.lifecyle_analysis_json.map(item => {
            return {
                date: new Date(item.reporting_month_yyyymm),
                median: item.gp_median,
                lowerBound: item.gp_q1,
                upperBound: item.gp_q3
            };
        });
    
        $("#gamesPlayedPercentile").dxChart({
            dataSource: gamesPlayedData,
            commonSeriesSettings: {
                argumentField: 'date',
                type: 'line'
            },
            series: [
                {
                    valueField: 'median',
                    name: 'Games Played Median',
                    color: orangeColor,
                    type: 'line'
                },
                {
                    rangeValue1Field: 'lowerBound',
                    rangeValue2Field: 'upperBound',
                    name: 'Games Played 90% Confidence Interval',
                    color: orangeColor,
                    type: 'rangeArea'
                }
            ],
            valueAxis: {
                title: {
                    text: 'Games Played Percentile'
                }
            },
            argumentAxis: {
                type: 'continuous',
                argumentType: 'datetime',
                grid: {
                    visible: true
                },
                label: {
                    format: 'monthAndYear'
                }
            },
            tooltip: {
                enabled: true,
                customizeTooltip: function(arg) {
                    let text = arg.seriesName + ': ' + '<b>' + arg.valueText + '</b>';
                    if(arg.seriesType === 'rangeArea') {
                        text += '<br/>90% Confidence Interval: ' + '<b>' + arg.rangeValue1Text + ' - ' + arg.rangeValue2Text + '</b>';
                    }
                    return { text: text };
                }
            },
            legend: {
                verticalAlignment: 'bottom',
                horizontalAlignment: 'center'
            },
            title: {
                text: ''
            }
        });
    }
    
    

    //add links to .chart-box
    // Get all boxes
var boxes = document.querySelectorAll('.chart-box');

// Iterate over each box and add click event
boxes.forEach(function (box) {
    box.addEventListener('click', function () {
        // Get target popup ID from data-target attribute
        var target = this.dataset.target;
        
        // Collapse all popups
        var popups = document.querySelectorAll('.collapse');
        popups.forEach(function (popup) {
            if (popup.id !== target.slice(1)) { // Exclude the target popup from being collapsed
                $(popup).collapse('hide');
            }
        });
        
        // Toggle the target popup
        $(target).collapse('toggle');
    });
});










    // NEXT PART IS EXPERIMENTAL

        // 3. Adoption Over Time Chart initialization
        if ($('#adoptionOverTimeChart').length) {
            const adoptionData = reelGamesData.gameData.lifecyle_analysis_json.map(item => {
                return {
                    date: new Date(item.reporting_month_yyyymm),
                    terminals: item.terminals,
                    venues: item.venues,
                    games: item.games
                };
            });
    
            $("#adoptionOverTimeChart").dxChart({
                dataSource: adoptionData,
                commonSeriesSettings: {
                    argumentField: 'date',
                    type: 'line'
                },
                series: [
                    { valueField: 'terminals', name: 'Terminals', color: mainColor },
                    { valueField: 'venues', name: 'Venues', color: orangeColor },
                    { valueField: 'games', name: 'Games', color: darkBlueColor }
                ],
                argumentAxis: {
                    valueMarginsEnabled: false,
                    discreteAxisDivisionMode: 'crossLabels',
                    grid: {
                        visible: true
                    }
                },
                tooltip: {
                    enabled: true,
                    shared: true
                },
                legend: {
                    verticalAlignment: "top",
                    horizontalAlignment: "center"
                },
                title: "Adoption Over Time",
            });
        }
        
        // 4. revenueInsightsChart Chart initialization
        if ($('#revenueInsightsChart').length) {
            const revenueData = reelGamesData.gameData.lifecyle_analysis_json.map(item => ({
                date: new Date(item.reporting_month_yyyymm),
                gp_rpm: item.gp_rpm,
                tw_rpm: item.tw_rpm,
                gptw_rpm: item.gptw_rpm
            }));
    
            $("#revenueInsightsChart").dxChart({
                dataSource: revenueData,
                commonSeriesSettings: {
                    argumentField: 'date',
                    type: 'line'
                },
                series: [
                    { valueField: 'gp_rpm', name: 'GP RPM', color: mainColor },
                    { valueField: 'tw_rpm', name: 'TW RPM', color: orangeColor },
                    { valueField: 'gptw_rpm', name: 'GPTW RPM', color: darkBlueColor }
                ],
                argumentAxis: {
                    valueMarginsEnabled: false,
                    discreteAxisDivisionMode: 'crossLabels',
                    grid: {
                        visible: true
                    }
                },
                tooltip: {
                    enabled: true,
                    shared: true
                },
                legend: {
                    verticalAlignment: "top",
                    horizontalAlignment: "center"
                },
                title: "Revenue Insights",
            });
        }

            // DataGrid initialization
    if ($('#dataGridContainer').length) {
        $("#dataGridContainer").dxDataGrid({
            dataSource: reelGamesData.gameData.lifecyle_analysis_json,
            // other configuration options for the DataGrid
        });
    }

    // Monthly Performance Chart initialization
    if ($('#monthlyPerformanceChart').length) {
        const monthlyPerformanceData = reelGamesData.gameData.lifecyle_analysis_json.map(item => {
            return {
                month: new Date(item.reporting_month_yyyymm).toLocaleString('default', { month: 'short', year: 'numeric' }),
                rpm: item.gp_rpm
            };
        });

        $("#monthlyPerformanceChart").dxChart({
            dataSource: monthlyPerformanceData,
            title: "Monthly Analysis",
            series: {
                argumentField: 'month',
                valueField: 'rpm',
                name: 'Game Played RPM',
                type: 'bar',
                color: '#b91c1a', // Main color
            },
            legend: {
                position: "outside",
                horizontalAlignment: "center",
                verticalAlignment: "bottom"
            }
        });
    }

    // Quartile Analysis Chart initialization
    if ($('#quartileAnalysisChart').length) {
        const quartileData = reelGamesData.gameData.lifecyle_analysis_json.map(item => {
            return {
                month: new Date(item.reporting_month_yyyymm).toLocaleString('default', { month: 'short', year: 'numeric' }),
                q1: item.gp_q1,
                median: item.gp_median,
                q3: item.gp_q3
            };
        });

        $("#quartileAnalysisChart").dxChart({
            palette: ['#b91c1a', '#192f3d', '#d4af37'], // Main, Dark Blue, Gold
            dataSource: quartileData,
            commonSeriesSettings: {
                argumentField: 'month',
                type: 'rangeBar',
                minBarSize: 2,
            },
            series: [
                {
                    rangeValue1Field: 'q1',
                    rangeValue2Field: 'q3',
                    name: 'Games Played RPM Q1-Q3',
                }
            ],
            valueAxis: {
                title: {
                    text: 'RPM Value',
                },
            },
            tooltip: {
                enabled: true,
            },
            argumentAxis: {
                label: {
                    format: 'month',
                },
            },
            legend: {
                verticalAlignment: 'bottom',
                horizontalAlignment: 'center',
            },
            title: 'Quartile Analysis',
        });
    }
});

// Toggle the chevron icon on the Game Performance section
jQuery(document).ready(function($) {
    $('#collapseGamePerformance').on('show.bs.collapse', function () {
        // Set the icon to point up (expanded state)
        $('#chevron-button-gameperformance .chevron i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }).on('hide.bs.collapse', function () {
        // Set the icon to point down (collapsed state)
        $('#chevron-button-gameperformance .chevron i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
});

