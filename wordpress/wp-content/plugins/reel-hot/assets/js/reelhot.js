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

    // Assuming reelHotData is available and contains the data for the new plugin
    if ($('#reelHotChart').length) {
        const reelHotChartData = reelHotData.gameData.map(item => {
            return {
                // You'll adjust the fields based on your new data structure
                month: new Date(item.reporting_month_yyyymm).toLocaleString('default', { month: 'short', year: 'numeric' }),
                someValue: item.someRelevantField
            };
        });

        $("#reelHotChart").dxChart({
            dataSource: reelHotChartData,
            title: "ReelHot Monthly Analysis",
            series: {
                argumentField: 'month',
                valueField: 'someValue',
                name: 'Some Metric Name',
                type: 'bar',
                color: mainColor
            },
            legend: {
                position: "outside",
                horizontalAlignment: "center",
                verticalAlignment: "bottom"
            }
        });
    }
});
