import moment from "moment-timezone";
import Constants from "../../../Constants";


const prepareDataForLiveAttenChart = (recurrenceData, timeInterval) => {
    let chartData = [];
    let logInUsers = 0;
    let logOutUsers = 0;
    let average = 0;

    if (recurrenceData?.attendance_data.length === 0) {
        chartData[0] = [timeInterval[0], 0, 0, 0]
    } else {
        timeInterval.forEach((blockPoint, i) => {
            chartData[i] = [blockPoint, logInUsers, logOutUsers, average];
        })

        let lastIndex = timeInterval.length - 1;
        let today = moment().format("YYYY-MM-DD");

        recurrenceData?.attendance_data.forEach((data, index) => {

            let filling = false;
            let fillingIndex = data.action === "login" ? 1 : 2;

            chartData.forEach((blockPoint, i) => {
                if (
                    (
                        filling === true
                        || (filling === false && blockPoint[0].format("HH:mm") <= moment(data.time).format("HH:mm") && (((lastIndex !== i) && timeInterval[i + 1].format("HH:mm") >= moment(data.time).format("HH:mm")) || i === lastIndex))
                    )
                    &&
                    (blockPoint[0].format("YYYY-MM-DD") != today || blockPoint[0].format("HH:mm") <= moment().format("HH:mm"))
                ) {

                    if (fillingIndex === 1) {
                        chartData[i][3] = chartData[i][3] + 1;
                    } else {
                        chartData[i][3] = chartData[i][3] - 1;
                    }

                    chartData[i][fillingIndex] = chartData[i][fillingIndex] + 1;
                    filling = true;
                }
            })
        })
        // Remove the continues data for today event
        chartData = chartData.filter(c =>
            (c[0].format("YYYY-MM-DD") != today || c[0].format("HH:mm") <= c[0].format("HH:mm")) && (c[1] || c[2])
        )
    }

    // Convert event duration in Date Time object
    chartData = chartData.map((c, i) => {
        c[0] = new Date(c[0].format(Constants.DATE_TIME_FORMAT));
        return c;
    })
    return chartData;
}

const prepareDataForPeekAttenChart = (recurrenceData, timeInterval) => {
    let chartDataForAllGrade = [];
    let chartDataForManager = [];
    let chartDataForEmployee = [];
    let chartDataForExecutive = [];
    let chartDataForOther = [];

    // Prepare empty data set for chart
    timeInterval?.forEach((blockPoint, i) => {
        chartDataForAllGrade[i] = [blockPoint, null, null, null];
        chartDataForManager[i] = [blockPoint, null, null, null];
        chartDataForEmployee[i] = [blockPoint, null, null, null];
        chartDataForExecutive[i] = [blockPoint, null, null, null];
        chartDataForOther[i] = [blockPoint, null, null, null];
    })

    const gradeTypes = {
        manager: chartDataForManager,
        employee: chartDataForEmployee,
        other: chartDataForOther,
        executive: chartDataForExecutive
    };

    let lastIndex = timeInterval.length - 1;
    let today = moment().format("YYYY-MM-DD");
    recurrenceData?.attendance_data.forEach((data) => {

        let filling = false;
        let fillingIndex = data.action === "login" ? 1 : 2;

        chartDataForAllGrade.forEach((blockPoint, i) => {
            if (
                (
                    filling === true
                    || (filling === false && blockPoint[0].format("HH:mm") <= moment(data.time).format("HH:mm") && (((lastIndex !== i) && timeInterval[i + 1].format("HH:mm") >= moment(data.time).format("HH:mm")) || i === lastIndex))
                )
                &&
                (blockPoint[0].format("YYYY-MM-DD") != today || blockPoint[0].format("HH:mm") <= moment().format("HH:mm"))
            ) {

                if (fillingIndex === 1) {
                    gradeTypes[data.grade][i][3] = gradeTypes[data.grade][i][3] + 1;
                    chartDataForAllGrade[i][3] = chartDataForAllGrade[i][3] + 1;
                } else {
                    gradeTypes[data.grade][i][3] = gradeTypes[data.grade][i][3] - 1;
                    chartDataForAllGrade[i][3] = chartDataForAllGrade[i][3] - 1;
                }

                gradeTypes[data.grade][i][fillingIndex] = gradeTypes[data.grade][i][fillingIndex] + 1;
                chartDataForAllGrade[i][fillingIndex] = chartDataForAllGrade[i][fillingIndex] + 1;
                filling = true;
            }

        })
    })

    // Convert event duration in Date Time object and remove the login and logout users count data
    let map = (c, i) => {
        if (c[3] === null) {
            c = [c[0], 0, 0, 0];
        }
        c[0] = new Date(c[0].format(Constants.DATE_TIME_FORMAT));
        c[1] = c[3]
        c.splice(1, 2)
        return c;
    }

    chartDataForAllGrade = chartDataForAllGrade.map(map)
    chartDataForEmployee = chartDataForEmployee.map(map)
    chartDataForManager = chartDataForManager.map(map)
    chartDataForExecutive = chartDataForExecutive.map(map)
    chartDataForOther = chartDataForOther.map(map)

    return {
        allGradeChartData: chartDataForAllGrade,
        chartDataForEmployee: chartDataForEmployee,
        chartDataForManager: chartDataForManager,
        chartDataForExecutive: chartDataForExecutive,
        chartDataForOther: chartDataForOther
    };
}

export default {
    prepareDataForLiveAttenChart: prepareDataForLiveAttenChart,
    prepareDataForPeekAttenChart: prepareDataForPeekAttenChart,
};