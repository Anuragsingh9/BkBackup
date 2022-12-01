const findMaxLimit = (target, dimension = 1, addPercent=20) => {
    let max = 0;
    target.forEach((data) => {
        max = max < data[dimension] ? data[dimension] : max
    });
    return max + Math.ceil(max * (addPercent/100));
}

let EventAnalyticsHelper = {
    findMaxLimit: findMaxLimit,
}

export default EventAnalyticsHelper;