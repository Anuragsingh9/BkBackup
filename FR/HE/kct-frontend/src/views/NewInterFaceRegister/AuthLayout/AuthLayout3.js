import React, {useEffect, useRef, useState} from 'react';
import authActions from '../../../redux/actions/authActions';
import {KeepContact as KCT} from '../../../redux/types';
import Header from '../../NewInterFace/Header/Header.js';
import Footer from '../../NewInterFace/Footer/Footer.js';
import Helper from '../../../Helper.js';
import {Provider as AlertContainer, useAlert } from 'react-alert';
import '../Registration/Registration.css';
import {connect} from 'react-redux';
import _ from 'lodash';
import {useParams} from "react-router-dom";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for wrapper design of various QSS pages without event details
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
const AuthLayoutJustHeader = (props) => {
    // child component which can be passed inside the wrapper components in routes as props
    const {Child} = props;
    // event data state
    const [event_data, setEvent] = useState({});
    // data load state
    const [dataLoad, setLoad] = useState(false);

    const {event_uuid} = useParams();

    // Initialisation fo message / alert ref to show alerts on success or error.

    const msg = useAlert();

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
        props.getEventDetails(event_uuid).then((res) => {
            if (res.data.status) {
                props.setEventDetailsData(res.data.data);
                setEvent(res.data.data);
                setLoad(true);
            }
        })
            .catch((err) => {
                msg.show && msg.show(Helper.handleError(err), {type: "error"})
            })
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
                    <Child {...props} />
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
)(AuthLayoutJustHeader);