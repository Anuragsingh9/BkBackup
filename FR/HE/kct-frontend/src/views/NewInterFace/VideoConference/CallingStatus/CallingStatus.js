import React from 'react';
import {connect} from 'react-redux';
import _ from 'lodash';
import './calling.css'

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to show calling notification for calling user
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {UserBadge} props.spaceHostData Space host data in form of user badge
 * @param {Object} props.show To indicate calling notification show or not
 * @param {Number} props.calledUserId User id of called user
 * @returns {JSX.Element}
 * @constructor
 */
const CallingStatus = (props) => {
    const spaceHost = props.spaceHostData[0];
    if (props.show) {
        if (props.calledUserId && (_.has(spaceHost, ['user_id'])) && props.calledUserId == spaceHost.user_id) {
            return (
                <div className="calling-bar no-texture">
                    <h3>Calling {spaceHost.fname + ' ' + spaceHost.lname} </h3>
                </div>
            )
        } else {
            return (
                <div className="calling-bar no-texture">
                    <h3>Calling <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </h3>
                </div>
            )
        }
    } else {
        return (<div />)
    }


}

const mapStateToProps = (state) => {
    return {
        spaceHostData: state.NewInterface.interfaceSpaceHostData,
    }
}
export default connect(mapStateToProps, null)(CallingStatus);