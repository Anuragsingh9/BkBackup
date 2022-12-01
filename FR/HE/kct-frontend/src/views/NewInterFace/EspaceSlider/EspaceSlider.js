import React, {useRef, useState} from "react";
import Slider from "react-slick";
import {Provider as AlertContainer, useAlert } from 'react-alert';
import _ from 'lodash';
import {connect} from "react-redux";
import eventActions from "../../../redux/actions/eventActions";
import Arrow from "./Arrow";
import Helper from '../../../Helper.js';
import ParticularESpace from "./ParticularESpace";
import {KeepContact as KCT} from '../../../redux/types';
import "./slick.css";
import "./slick-theme.css";
import "./EspaceSlider.css"

let sliderTimeOut = null;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component allows online users to browse through all the spaces that are created by a Pilot during
 * Event Creation. Participants can join the Spaces by clicking on it and view the online members inside of it.
 * Each participant must be inside one Space at any point of time during the Event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {SpaceData} props.active_space
 * @param {Object} props.alert Reference object for displaying notification popup
 * @param {Function} props.changeSpace To trigger the api to change the space
 * @param {EventData} props.event_data Current event data
 * @param {Boolean} props.event_during To indicate if the event is live or not
 * @param {InterfaceSpaceData} props.event_space Current spaces data
 * @param {InterfaceSliderData} props.sliderData All spaces with pages required and sorted by type of spaces
 * @param {Function} props.sortSpaces To sort the available spaces in the event by type of space
 * @param {Function} props.spaceJoin To trigger the join space method for redux and api
 * @param {Boolean} props.spacesActive To indicate if the space is currently joined or not
 * @param {Function} props.triggerPagination To change the current page in spaces slider
 * @param {String} props.welcome_txt Welcome text value for the current space joined
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const SliderSPersons = (props) => {
    const [displayRightArrow, setDisplayRightArrow] = useState(true);
    const [displayLeftArrow, setDisplayLeftArrow] = useState(false);
    const [page, setPage] = useState(1);
    const [list, setlist] = useState(2);
    const [currentSlide, setCurrentSlide] = useState(0);
    const [slidersize, setSlidersize] = useState(5);
    const slider = useRef(null)
    const alert = useAlert();

    /**
     * @deprecated
     */
    const next = () => {
        const spaces = props.event_space.spaces;
        const vipDuoSelectedSpace = props.event_space.vipDuoSelectedSpace
        const lastPage = !((currentSlide - spaces.length < 0)
            && (currentSlide - spaces.length >= -(slidersize - vipDuoSelectedSpace.length)))
        slider.current.slickNext();
    };

    /**
     * @deprecated
     */
    const previous = () => {
        const displayLeftArrow = currentSlide !== 0;
        slider.current.slickPrev();
    };


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will handle the arrows(to move slider left and right direction if spaces are more then
     * 5).If user visit most left space then left arrow will not be visible and vice versa.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} currentSlide Current slided spaces count from left
     */
    const setArrowDisplay = (currentSlide, pos) => {
        const spaces = props.event_space.spaces;
        const displayLeftArrow = currentSlide !== 0;
        const displayRightArrow = !((currentSlide - spaces.length < 0) && (currentSlide - spaces.length > -2));

        setDisplayRightArrow(displayRightArrow);
        setDisplayLeftArrow(displayLeftArrow);
        setCurrentSlide(currentSlide);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when user reached at the last space(in left/right) in the space slider.
     * This function will remove all last visible spaces and only show remaining spaces in slider.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const resetSlider = () => {
        clearTimeout(sliderTimeOut);
        const global = this;
        sliderTimeOut = setTimeout(() => {
            props.sortSpaces(1);
            // global.setState({page:1});
            setPage(1);
        }, 3000);
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is used to move slider in left direction(one step in one click).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page Current page number spaces slider
     */
    const fetchLastPage = (page) => {
        props.sortSpaces(page - 1);
        setPage(page - 1);
        resetSlider();
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is used to move slider in right direction(one step in one click).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page Current page number spaces slider
     */
    const fetchNextPage = (page) => {
        props.sortSpaces(page + 1);
        setPage(page + 1)
        resetSlider();
    }

    // for slider costum settings
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

    const {sliderData, event_data} = props;
    return (
        <div tabIndex={0} onFocus={() => {
        }}>
            <AlertContainer
                ref={alert}
                {...Helper.alertOptions}
            />
            <div className="kct-2-first">
                {page > 1 &&
                <Arrow
                    direction="left"
                    previous={() => {
                        fetchLastPage(page)
                    }}
                />
                }

                <section className="">
                    <div className={`${props.event_space.spaces.length <= 1 ? "lessThenFiveSpace" : ""}`}>
                        <Slider {...settings} ref={slider}>
                            {sliderData.spaces.map((val, key) => {
                                return (<ParticularESpace
                                        spacesActive={props.spacesActive}
                                        welcome_txt={props.welcome_txt}
                                        msg={alert}
                                        event_uuid={event_data.event_uuid}
                                        spaceJoin={props.spaceJoin}
                                        event_during={props.event_during}
                                        changeSpace={props.changeSpace}
                                        index={key}
                                        event_space={props.event_space}
                                        active_space={active_space.space_uuid}
                                        val={val}
                                        triggerPagination={props.triggerPagination}
                                    />
                                );
                            })}
                        </Slider>
                    </div>
                </section>
                {_.has(sliderData, ['maxPage']) && sliderData.maxPage > page &&
                <Arrow
                    direction="right"
                    next={() => {
                        fetchNextPage(page)
                    }}
                />
                }
            </div>
        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        sortSpaces: (data) => dispatch({type: KCT.NEW_INTERFACE.SORT_SPACES, payload: data}),
        changeSpace: (data) => dispatch({type: KCT.NEW_INTERFACE.CHANGE_SPACES, payload: data,}),
        spaceJoin: (id) => dispatch(eventActions.Event.spaceJoin(id)),
    }
}

export default connect(null, mapDispatchToProps)(SliderSPersons);



