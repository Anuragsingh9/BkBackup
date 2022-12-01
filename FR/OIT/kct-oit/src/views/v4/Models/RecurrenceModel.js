import moment from "moment-timezone";
import Constants from "../../../Constants";

const RecurrenceModel = {
    event_rec_start_date: new moment(),
    event_rec_type: Constants.recurrenceType.NONE,
    event_rec_end_date: new moment().clone().add(1, 'd'),
    event_rec_month_date: 1,
    event_rec_interval: 1,
    event_rec_month_type: 1,
    event_rec_on_month_week: 1,
    event_rec_on_month_week_day: "Monday",
    event_rec_selected_weekday: {
        mon: true,
        tue: false,
        wed: false,
        thu: false,
        fri: false,
        sat: false,
        sun: false,
    },
}

Object.freeze(RecurrenceModel);

export default RecurrenceModel;

