import React, {useRef, useState} from "react";
import {connect} from "react-redux";
import {Provider as AlertContainer, useAlert } from 'react-alert';
import {KeepContact as KCT} from '../../../redux/types';
import Helper from '../../../Helper.js';
import SpaceItem from "./SpaceItem";
import authActions from "../../../redux/actions/authActions";
import "./css/slick.css";
import "./css/slick-theme.css";
import "./css/EspaceSlider.css"

let sliderTimeOut = null;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper component which is developed to manage join space action on quick registration page.
 * From here user can select any space(except restrict space for specific type roles eg- VIP space) to checkin on event
 * dashboard page once click on 'Join the event' button from quick registration page.
 * default space will be selected by default.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props.alert Reference object for displaying notification popup
 * @param {Function} props.changeSpace To trigger the api to change the space
 * @param {SpaceData} props.currentSpace Current Space Object
 * @param {EventData} props.eventData Current event data
 * @param {String} props.eventUuid Current event uuid
 * @param {Function} props.setCurrentSpace To update the current space object in redux store
 * @param {InterfaceSliderData} props.sliderData All spaces with pages required and sorted by type of spaces
 * @param {Function} props.sortSpaces To sort the available spaces in the event by type of space
 * @param {Function} props.spaceJoin To trigger the join space method for redux and api
 * @param {String} props.welcome_txt Welcome text value for the current space joined
 * @param {String} props.welcome_txt
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const Spaces = (props) => {
    const [displayRightArrow, setDisplayRightArrow] = useState(true);
    const [displayLeftArrow, setDisplayLeftArrow] = useState(false);
    const [page, setPage] = useState(1);
    const [list, setList] = useState(2);
    const [currentSlide, setCurrentSlide] = useState(0);
    const [slidersize, setSliderSize] = useState(5);
    const alert = useAlert();
    const slider = useRef(null)

    const {sliderData, eventUuid, currentSpace, setCurrentSpace} = props;

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle to display arrows(button to move space slider in right and left) when space
     * count will be more then 5.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} currentSlide Current slided spaces count from left
     * @param {Number} pos Current position number of spaces from left scrolled
     */
    const setArrowDisplay = (currentSlide, pos) => {
        const spaces = props.spaces;
        const displayLeftArrow = currentSlide !== 0;
        const displayRightArrow = !((currentSlide - spaces.length < 0) && (currentSlide - spaces.length > -2));
        setDisplayRightArrow(displayRightArrow);
        setDisplayLeftArrow(displayLeftArrow);
        setCurrentSlide(currentSlide);
    };


    const handleBlur = () => {
    }

    //slider setting can be set custom
    const {active_space} = props;
    var settings = {
        swipe: false,
        dots: true,
        infinite: false,
        speed: 500,
        arrow: true,
        // slidesToShow: 3.35,
        slidesToShow: 5,
        slidesToScroll: 1,
        variableWidth: true,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,

                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2
                }
            }
        ],
        nextArrow: null,
        prevArrow: null,
        afterChange: (currentSlide, pos) => setArrowDisplay(currentSlide, pos),
    };


    return (
        <div tabIndex={0} onFocus={() => {
        }} onBlur={handleBlur}>
            <AlertContainer
                ref={alert}
                {...Helper.alertOptions}
            />
            <div className="kct-2-first">
                <div className="p-sm-0 col-md-12 dark-space-inner d-flex flex-wrap ">
                    {sliderData && sliderData.map((val, key) => {
                        return (<SpaceItem
                                welcome_txt={props.welcome_txt}
                                t={props.t}
                                msg={props.alert}
                                event_uuid={eventUuid}
                                setCurrentSpace={setCurrentSpace}
                                spaceJoin={props.spaceJoin}
                                event_during={false} changeSpace={props.changeSpace} index={key}
                                event_space={props.event_space}
                                active_space={currentSpace}
                                eventData={props.eventData}
                                val={val} />
                        );
                    })}
                </div>
            </div>
        </div>
    )
}


const mapDispatchToProps = (dispatch) => {
    return {
        sortSpaces: (data) => dispatch({type: KCT.NEW_INTERFACE.SORT_SPACES, payload: data}),
        changeSpace: (data) => dispatch({type: KCT.NEW_INTERFACE.CHANGE_SPACES, payload: data,}),
        spaceJoin: (data) => dispatch(authActions.Auth.registerSpaceMood(data)),
    }
}
export default connect(null, mapDispatchToProps)(Spaces);
