import React, {useEffect, useRef, useState} from 'react';
import authActions from '../../../redux/actions/authActions';
import {KeepContact as KCT} from '../../../redux/types';
import Header from '../../NewInterFace/Header/Header.js';
import Footer from '../../NewInterFace/Footer/Footer.js';
import '../Registration/Registration.css';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import Helper from '../../../Helper.js';
import {connect} from 'react-redux';
import _ from 'lodash';
import {useTranslation} from 'react-i18next';
// eslint-disable-next-line no-unused-vars
import {GraphicsData, ColorRGBA} from '../../../Model';
import {useParams} from "react-router-dom";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for wrapper design of various QSS pages which includes event details
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props for the AuthLayout component
 * @param {React.Component} props.Child Child component to be rendered
 * @param {Function} props.getEventDetails [Dispatcher|API] Method used to get the event details from api
 * @param {Function} props.setEventDetailsData [Dispatcher] This method updates the redux to store the event data
 * @param {GraphicsData} props.graphics_data [State] This variable holds the current graphics data set in redux
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const AuthLayout = (props) => {
    // child component which can be passed inside the wrapper components in routes as props
    const {Child} = props;
    const {t} = useTranslation('qss');

    /**
     * @var
     */
    // event data state
    const [event_data, setEvent] = useState({});
    // event date state
    const [event_date, setDate] = useState('');
    // data load state
    const [dataLoad, setLoad] = useState(false);

    // Initialisation for message / alert ref to show alerts on success or error.
    const msg = useAlert();

    const {event_uuid} = useParams();

    useEffect(() => {
        getEvent();
    }, []);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event details and store them in redux
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     **/
    const getEvent = () => {
        if (event_uuid !== undefined) {
            props.getEventDetails(event_uuid).then((res) => {
                if (res.data.status) {
                    props.setEventDetailsData(res.data.data);
                    const eventData = res.data.data;
                    const date = Helper.getTimeUserTimeZone('Europe/Paris', `${eventData.date} ${eventData.start_time}`);
                    setDate(date);
                    setEvent(res.data.data);
                    setLoad(true);
                }
            })
                .catch((err) => {
                    msg && msg && msg.show(Helper.handleError(err), {type: "error"})
                })
        } else {
            setLoad(true);
        }
    }

    if (!dataLoad) {
        return (<Helper.pageLoading />)
    }

    return (

        <div>
            <AlertContainer ref={msg} {...Helper.alertOptions} />
            <Header
                event_data={{
                    ...event_data,
                    header_line_one: _.has(event_data, ['header_line_1']) ? event_data.header_line_1 : '',
                    header_line_two: _.has(event_data, ['header_line_2']) ? event_data.header_line_2 : ''
                }}
                dropdown={false}
            />
            <div className="content-height">
                <div className="container">
                    <div className="welcome-sign kct-customization">
                        <h2 className="enter-note  heading-color">{t("Welcome")}</h2>
                        <div className="row">
                            <div className="col-lg-12 mt-20">
                                <div className="head-login heading-color">
                                    <h4>{_.has(event_data, ['event_title']) && event_data.event_title}</h4>
                                    <h4>{t("on")} {Helper.dateTimeFormat(event_date, 'MMMM DD, YYYY')} {t("at")} {Helper.dateTimeFormat(event_date, 'hh:mm A')}</h4>
                                </div>
                            </div>
                            <div className="col-lg-12 px-0 form-regi">
                                <Child {...props} />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <Footer graphics_data={props.graphics_data} />
        </div>
    )

}

const mapDispatchToProps = (dispatch) => {
    return {
        getEventDetails: (id) => dispatch(authActions.Auth.getEventDetails(id)),
        setEventDetailsData: (data) => dispatch({type: KCT.AUTH.EVENT_DETAILS, payload: data}),
    }
}

const mapStateToProps = (state) => {
    return {
        graphics_data: state.NewInterface.interfaceGraphics,
    };
};


export default connect(
    mapStateToProps,
    mapDispatchToProps
)(AuthLayout);