import Constants from "../../../../Constants"

const colorsArray = ["#3498DB","#F39C12", "#1ABC9C","#FFA07A","#AF7AC5","#F1C40F","#1ABC9C","#7F8C8D"]
const ChartModel = {
    barChart: (properties) => {
        return ({
            seriesType: "bars",
            bar: {groupWidth: 50, },
            backgroundColor: Constants.colors.primary_2,
            animation: {
                duration: 200,
                startup: true,
            },
            chartArea: {left: 50, width: '100%',height:160},
            colors: colorsArray,
            legend: {position: "top", alignment: "center"},
            vAxis: {
                title: "User Count",
                viewWindow: {
                    min: 0
                }
            },
            hAxis: {
                viewWindow: {
                    min: 0
                }
            },
            ...properties,
        })
    },
    pieChart: (properties) => {
        return ({
            backgroundColor: Constants.colors.primary_2,
            legend: {position: "top", alignment: "center"},
            colors: colorsArray,
            animation: {
                duration: 200,
                startup: true,
            },
            ...properties,
        })
    },
    lineChart: (properties) => {
        return ({
            backgroundColor: Constants.colors.primary_2,
            chartArea: {left: 50, width: '90%',height: '80%'},
            colors: colorsArray,
            legend: {position: "top", alignment: "center"},
            vAxis: {
                title: "User Count",
                minValue: 0,
                viewWindow: {
                    min: 0,
                }
            },
            hAxis: {
                format: "HH:mm"
            },
            explorer: {
                action:  ['dragToPan', 'rightClickToReset', 'dragToZoom'],
                axis: 'horizontal',
                keepInBounds: true,
                maxZoomIn: 0.5,
                maxZoomOut: 8,
            },
            curveType: 'function',
            ...properties,
        })
    },
    columnChart: (properties) => {
        return ({
            backgroundColor:Constants.colors.primary_2,
            chartArea: {left: 50, width: '90%',height: '80%'},
            colors: colorsArray,
            vAxis: {
                title: "User Count",
                minValue: 0,
                viewWindow: {
                    min: 0,
                },
                gridlines: {
                    multiple: 1,
                },
            },
            hAxis: {
                format: "HH:mm",
                gridlines: {
                    multiple: 1,
                }
            },
            legend: "none",
            explorer: {
                action:  ['dragToPan', 'rightClickToReset', 'dragToZoom'],
                axis: 'horizontal',
                keepInBounds: true,
                maxZoomIn: 0.5,
                maxZoomOut: 8,
            },
            curveType: 'function',
            ...properties,
        })
    },
    comboChart: (properties) => {
        return ({
            vAxis: {title: "Cups"},
            chartArea: {left: 50, width: '90%'},
            hAxis: {title: "Month"},
            seriesType: "bars",
            // series: {1: {type: "line"},3: {type: "line"},5: {type: "line"},},
            backgroundColor: Constants.colors.primary_2,
            colors: colorsArray,
            legend: {position: "top", alignment: "center"},
            ...properties,
        })
    }
}

export default ChartModel;