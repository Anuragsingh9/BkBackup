import React, {useEffect, useState} from 'react';
import moment from 'moment-timezone';
import {useTranslation} from 'react-i18next';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for CountDownTimer in qss and dashboard.
 * This component will calculate the time remaining to start the event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Boolean} props.fromQuickSignUp To indicate if component opening in registration page mode
 * @param {Boolean} props.customisation To indicate to apply color customization or not
 * @param {Boolean} props.is_event_end To indicate if event is ended or not as ended event will have different text
 * @param {Boolean} props.event_during To indicate if event is live so event live text can be displayed instead timer
 * @param {Object} props.page_Customization Event time data to show
 * @param {Moment} props.page_Customization.event_date Date of event
 * @param {String} props.page_Customization.event_start_time Start time of event
 * @param {String} props.page_Customization.time_zone Time zone to apply on time calculation
 * @class
 * @component
 * @returns {JSX.Element|null}
 * @constructor
 */
const CountDownTimer = (props) => {
    const [timeZone, SetTImeZone] = useState(
        props.page_Customization.time_zone ? props.page_Customization.time_zone : "Europe/Paris"
    );
    const [days, SetDays] = useState(0);
    const [hours, SetHours] = useState(0);
    const [showTimer, setShowTimer] = useState(true);
    const [minutes, SetMinutes] = useState(0);
    const [seconds, SetSeconds] = useState(0);
    var currentTime = new Date().toLocaleString("en-US", {timeZone: timeZone});

    const {t} = useTranslation('timer')
    // useEffect handles time updates
    useEffect(() => {
        const {event_start_time, event_date, time_zone} = props.page_Customization;

        setInterval(() => {

            var aestTime = new Date().toLocaleString("en-US", {timeZone: time_zone});
            const now = moment(aestTime).valueOf();
            const countDownDate = moment(`${event_date} ${event_start_time}`).valueOf();

            const distance = countDownDate - now;

            if (distance > 0) {
                setShowTimer(true)
            } else {
                setShowTimer(false)
            }

            SetDays(Math.floor(distance / (1000 * 60 * 60 * 24)));
            SetHours(
                Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
            );
            SetSeconds(Math.floor((distance % (1000 * 60)) / 1000));
            let min = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let sec = Math.floor((distance % (1000 * 60)) / 1000);
            if (min.toString().length < 2) {
                const newmin = min;
                SetMinutes(newmin);
            } else {
                SetMinutes(min.toString());
            }
            let Countsec1 = "";
            let Countsec2 = "";
            if (sec.toString().length < 2) {
                const sec1 = "0" + sec;
                Countsec1 = parseInt(sec1[0]);
                Countsec2 = Math.floor((sec1 / 1) % 10);
            } else {
                const sec1 = sec.toString();
                Countsec1 = parseInt(sec1[0]);
                Countsec2 = Math.floor((sec1 / 1) % 10);
            }

        }, 1000);
    }, []);
    const {is_event_end, fromQuickSignUp, customisation} = props
    //Quick signin-signup countdown
    //conditionally rendering for Quick signin-signup and dashboard
    if (fromQuickSignUp) {

        if (customisation) {
            if ((days < 0 || hours < 0 || minutes < 0 || seconds < 0)) {
                return null;
            } else {
                return (
                    <div className="after-timer">
                        <div className="count-main">
                            <div className="count-outer no-border">
                                <div className="count-inner-box no-border">
                                    <p className="count-main-title">{days} <span
                                        className="countspan"> {t('day')}</span></p>
                                </div>
                            </div>
                            <div className="count-outer no-border">
                                <div className="count-inner-box no-border">
                                    <p className="count-main-title">{hours} <span className="countspan"> h </span></p>
                                </div>
                            </div>
                            <div className="count-outer no-border">
                                <div className="count-inner-box no-border">
                                    <p className="count-main-title">{minutes} <span className="countspan"> mins </span>
                                    </p>
                                </div>
                            </div>
                            <div className="count-outer no-border">
                                <div className="count-inner-box no-border">
                                    <p className="count-main-title">{seconds} <span className="countspan"> secs </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                )
            }
        }

        return (
            // this countdown timer shows after event ends
            <div className="position-relative sm-positionRelative">
                <div className="timer-para">
                    {is_event_end ? <>
                            <p className="timer-text timer-text1">{t("Your")}</p>
                            <p className="timer-text timer-text2">Event</p>
                            <p className="timer-text timer-text1">{t("has ended")}</p>
                            <p className="timer-text timer-text1">{t("See you")}</p>
                            <p className="timer-text timer-text1">{t("at next")}
                                <span className=" timer-text2">Event</span>
                            </p>
                        </> :
                        <>
                            <p className="timer-text timer-text1">{t("Your")}</p>
                            <p className="timer-text timer-text2">Event</p>
                            <p className="timer-text timer-text3">{t("will start")}</p>
                            <p className="timer-text timer-text4">{t("automatically")}</p>
                            <p className="timer-text timer-text5">{t("in")}</p>
                        </>
                    }
                </div>
                {(days < 0 || hours < 0 || minutes < 0 || seconds < 0) ?

                    null
                    :
                    <div className="count-main">
                        <div className="count-outer day">
                            <div className="count-inner-box">
                                <p className="count-main-title">{days} <span className="countspan"> {t('day')}</span>
                                </p>
                            </div>
                        </div>
                        <div className="sec-hour">
                            <div className="count-outer second">
                                <div className="count-inner-box">
                                    <p className="count-main-title">{seconds} <span className="countspan"> secs </span>
                                    </p>
                                </div>
                            </div>
                            <div className="count-outer hour">
                                <div className="count-inner-box">
                                    <p className="count-main-title">{hours} <span className="countspan"> h </span></p>
                                </div>
                            </div>
                        </div>
                        <div className="count-outer minutes">
                            <div className="count-inner-box">
                                <p className="count-main-title">{minutes} <span className="countspan"> mins </span></p>
                            </div>
                        </div>
                    </div>
                }

            </div>
        )

    }
    // this is for during the event
    if (props.event_during) {
        return null
    }
    return (
        <div className="position-relative sm-positionRelative">
            <div className="timer-para">
                {is_event_end ? <>
                        <p className="timer-text timer-text1">{t("Your")}</p>
                        <p className="timer-text timer-text2">Event</p>
                        <p className="timer-text timer-text1">{t("has ended")}</p>
                        <p className="timer-text timer-text1">{t("See you")}</p>
                        <p className="timer-text timer-text1">{t("at next")}
                            <span className=" timer-text2">Event</span>
                        </p>
                    </> :
                    <>
                        <p className="timer-text timer-text1">{t("Your")}</p>
                        <p className="timer-text timer-text2">Event</p>
                        <p className="timer-text timer-text3">{t("will start")}</p>
                        <p className="timer-text timer-text4">{t("automatically")}</p>
                        <p className="timer-text timer-text5">{t("in")}</p>
                    </>
                }
            </div>
            {(days < 0 || hours < 0 || minutes < 0 || seconds < 0) ?

                null
                :
                <div className="count-main">
                    <div className="count-outer no-border">
                        <div className="count-inner-box no-border">
                            <p className="count-main-title">{days} <span className="countspan"> {t('day')}</span></p>
                        </div>
                    </div>
                    <div className="count-outer no-border">
                        <div className="count-inner-box no-border">
                            <p className="count-main-title">{hours} <span className="countspan"> h </span></p>
                        </div>
                    </div>
                    <div className="count-outer no-border">
                        <div className="count-inner-box no-border">
                            <p className="count-main-title">{minutes} <span className="countspan"> mins </span></p>
                        </div>
                    </div>
                    <div className="count-outer no-border">
                        <div className="count-inner-box no-border">
                            <p className="count-main-title">{seconds} <span className="countspan"> secs </span></p>
                        </div>
                    </div>
                </div>
            }

        </div>
    )
}
export default CountDownTimer;