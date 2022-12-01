import moment from "moment-timezone";
import SpaceModel from "./SpaceModel";

moment.tz.setDefault("Europe/Paris");


class EventModel {

    constructor() {
        let currentTime = moment();
        this.event_uuid = "";
        this.event_title = "";
        this.event_start_date = currentTime.clone();
        this.event_start_time = currentTime.clone();
        this.event_end_time = currentTime.clone().add(1, 'hour');
        this.custom_link = {
            code: '',
            full_url: '',
        };
        this.event_space_host = "";
        this.event_description = "";
        this.event_spaces = [
            SpaceModel,
        ];
        this.event_conv_limit = 4;
    }

    get() {
        return {
            event_uuid: this.event_uuid,
            event_title: this.event_title,
            event_start_date: this.event_start_date,
            event_start_time: this.event_start_time,
            event_end_time: this.event_end_time,
            custom_link: this.custom_link,
            event_space_host: this.event_space_host,
            event_description: this.event_description,
            event_spaces: this.event_spaces,
            event_conv_limit: this.event_conv_limit,
        }
    }
}

export default EventModel;

