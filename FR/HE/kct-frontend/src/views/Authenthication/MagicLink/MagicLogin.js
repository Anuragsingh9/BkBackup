import React, {useEffect, useRef, useState} from 'react'
import eventActions from "../../../redux/actions/auth/index";
import {reactLocalStorage} from 'reactjs-localstorage';
import {useTranslation, withTranslation} from 'react-i18next';
import {NavLink} from 'react-router-dom'
import {Provider as AlertContainer, useAlert } from 'react-alert';
import {connect} from 'react-redux';
import Helper from '../../../Helper';
import _ from 'lodash';


const queryString = require('query-string');
/**
 * @deprecated
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.getEventDetails [Dispatcher|API] Method used to get the event details from api
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const MagicLogin = (props) => {

    const [user, setUser] = useState({fname: "", lname: ""});
    const [token, setToken] = useState('');
    const [eventId, setEventId] = useState('');
    const [eventDetails, setEventDetails] = useState({});
    const [eventDetailsLoad, setEventDetailsLoad] = useState(false)

    const msg = useAlert()

    const {t} = useTranslation('qss')

    /**
     *
     * getEventDetails
     * Function gets Event details
     * @param {*} id
     * @method
     */
    const getEventDetails = (id) => {
        try {

            props.getEventDetails(id)
                .then((res) => {
                    const data = res.data.data;
                    let token = localStorage.getItem('accessToken');
                    if (token){
                        props.history.push(`/dashboard/${eventId}`);
                    }else{
                        loginUser()
                    }
                })
                .catch((err) => {
                    msg && msg.show(Helper.handleError(err), {type: "error"})

                })

        } catch (err) {
            msg && msg.show(Helper.handleError(err), {type: "error"})
        }
    }

// useEffect hooks  updates states values when states values changes

    useEffect(() => {
        var params = queryString.parse(props.location.search);

        if (_.has(params, ['name']) && _.has(params, ['event_uuid']) && _.has(params, ['token'])) {

            const user = {
                fname: params.name.split(' ')[0],
                lname: params.name.split(' ')[1],
            };


            setUser(user);
            setToken(params.token);
            setEventId(params.event_uuid);

            getEventDetails(params.event_uuid)


        } else {

            msg && msg.show("Incorrect Url", {type: "error"})

        }

    }, [token, eventId])


    /**
     *
     * loginUser
     * Function pushed user to next page
     * @method
     *
     */

    const loginUser = () => {
        reactLocalStorage.set('accessToken', token);
        reactLocalStorage.set('fname', user.fname);
        reactLocalStorage.set('lname', user.lname);
        props.history.push(`/dashboard/${eventId}`);

    }


    if (eventDetailsLoad === false) {
        return (
            <React.Fragment>
                <AlertContainer ref={msg} {...Helper.alertOptions} />
                <Helper.pageLoading />
            </React.Fragment>
        )
    }

    return (
        <div className="container">
            <AlertContainer ref={msg} {...Helper.alertOptions} />
            <div className="clearfix text-center account-name">
                <h3 className="mt-0">For <strong>{reactLocalStorage.get('organisation_name') ? reactLocalStorage.get('organisation_name') : 'Account Name'}</strong>
                </h3>
            </div>
            <div className="row">
                <div className="resilience-login-form">
                    <div className="text-center w-100 d-inline">
                        <h4>{eventDetails.event_title} , {Helper.dateTimeFormat(eventDetails.date, 'MMMM DD, YYYY')} </h4>
                        <h4 className="mb-20">{t("Are you")} {user.fname} {user.lname} ?</h4>
                        <div className="col-xs-12 col-ms-12 pl-0 pr-0 mt-30">
                            <NavLink to={`/`} className="btn btn-primary mr-15">{t("No")}</NavLink>
                            <button type="button" className="btn btn-primary " onClick={loginUser}>{t("Yes")}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )

}

const mapDispatchToProps = (dispatch) => {
    return {
        getEventDetails: (id) => dispatch(eventActions.getEventDetails(id)),
    }
}

const mapStateToProps = (state) => {
    return {};
};
export default connect(mapStateToProps, mapDispatchToProps)(MagicLogin);
withTranslation('qss')(MagicLogin)

