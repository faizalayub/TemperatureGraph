
$.get(ROOT_PATH + '/data/asset/data/aqi-beijing.json', function (data) {

    console.log(data);
    
    const sampledata_1 = data.map(function (item) {
        return item[1];
    });

    const sampledata_2 = data.map(function (item) {
        return item[1] - 30;
    });

    const markliner = {
        silent: true,
        lineStyle: { color: '#333' },
        data: [{ yAxis: 50 }, { yAxis: 100 }, { yAxis: 150 }, { yAxis: 200 }, { yAxis: 300 }]
    };

    const visualmapper = {
        top: 50,
        right: 10,
        pieces: [
            { gt: 0, lte: 50, color: '#93CE07' },
            { gt: 50, lte: 100, color: '#FBDB0F' },
            { gt: 100, lte: 150, color: '#FC7D02' },
            { gt: 150, lte: 200, color: '#FD0100' },
            { gt: 200, lte: 300, color: '#AA069F' },
            { gt: 300, color: '#AC3B2A' }
        ],
        outOfRange: { color: '#999' }
    };

    myChart.setOption(
        (option = {
        title: {
            text: 'Beijing AQI',
            left: '1%'
        },
        tooltip: {
            trigger: 'axis'
        },
        grid: {
            left: '5%',
            right: '15%',
            bottom: '10%'
        },
        xAxis: {
            data: data.map(function (item) {
            return item[0];
            })
        },
        yAxis: {},
        toolbox: false,
        dataZoom: [{ startValue: '2014-06-01' }, { type: 'inside' }],
        visualMap: visualmapper,
        series: [{
                name: 'Zana Temperature',
                type: 'line',
                data: sampledata_1,
                markLine: markliner
            },{
                name: 'Faizal Temperature',
                type: 'line',
                data: sampledata_2,
                markLine: markliner
            }]
        })
    );
});