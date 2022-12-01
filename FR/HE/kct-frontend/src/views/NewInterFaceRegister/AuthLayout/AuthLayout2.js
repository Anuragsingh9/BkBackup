import React from 'react';
import '../Registration/Registration.css';
import {connect} from 'react-redux';
import Header from '../../NewInterFace/Header/Header.js';
import Footer from '../../NewInterFace/Footer/Footer.js';
import MyEventList from "../../MyEventList/MyEventList";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for wrapper design of various QSS pages which does not includes event details. Simple login
 * and Simple Otp page And a header without line 1 and line 2
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props for the AuthLayout component
 * @param {React.Component} props.Child Child component to be rendered
 * @param {GraphicsData} props.graphics_data [State] This variable holds the current graphics data set in redux
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
let AuthLayoutWithoutEvent = (props) => {
    // child component which can be passed inside the wrapper components in routes as props
    const {Child} = props;


    return (
        <div>
            <Header
                event_data={{
                    header_line_one: '',
                    header_line_two: ''
                }}
                dropdown={false}
            />
            <div className="content-height">
                <div className="container">

                    {Child}
                </div>
            </div>
            <Footer graphics_data={props.graphics_data} />
        </div>
    )

}

const mapStateToProps = (state) => {
    return {
        graphics_data: state.NewInterface.interfaceGraphics,
    };
};


AuthLayoutWithoutEvent =  connect(
    mapStateToProps,
    null
)(AuthLayoutWithoutEvent);

export default AuthLayoutWithoutEvent;