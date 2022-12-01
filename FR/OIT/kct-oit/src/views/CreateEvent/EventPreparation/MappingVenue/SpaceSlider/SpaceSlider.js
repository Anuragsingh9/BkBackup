import React, {useEffect, useRef, useState} from "react";
import Slider from "react-slick";
import "./slick.css";
import "./slick-theme.css";
import "./EspaceSlider.css";
import SpaceItem from "./SpaceItem";
import _ from "lodash";
import {connect} from "react-redux";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import AddIcon from "@material-ui/icons/Add";
import ChevronRightSharpIcon from "@mui/icons-material/ChevronRightSharp";
import ChevronLeftSharpIcon from "@mui/icons-material/ChevronLeftSharp";
import "./SpaceItem.css";

let sliderTimeOut = null;

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render all the spaces(in multiple space-venue type) in a horizontal
 * slider structure.It also includes 2 arrows which helps to move the slider left and right.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Array} props.spaceData Array of space data
 * @param {Object} props.spaceLines Space data
 * @param {String} props.spaceLines.spaceId Space uuid
 * @param {Function} props.showSpaceLine Function is used to show space data
 * @param {Function} props.deleteSpace Function is used to delete space
 * @param {String} props.selectedItem Selected space
 * @param {String} props.venueType Space type(mono, normal)
 * @param {Boolean} props.allowAdd Is used to add space or not
 * @returns {JSX.Element}
 * @constructor
 */
const SliderSPersons = (props) => {
    //states in component
    const [displayRightArrow, setDisplayRightArrow] = useState(true);
    const [displayLeftArrow, setDisplayLeftArrow] = useState(false);
    const [page, setPage] = useState(1);
    const [list, setlist] = useState(2);
    const [currentSlide, setCurrentSlide] = useState(1);
    const [slidersize, setSlidersize] = useState(2);
    const [spaceData, setSpaceData] = useState();
    const [newSpaceData, setNewSpaceData] = useState();

    const slider = useRef(null);
    const alert = useRef(null);

    // this hook re render the component when props changes  and update state
    useEffect(() => {
        setSpaceData(props.spaceData);
    }, [props]);

    // this hooK update new data when space line changes and remove the effect when component lifecycle ends
    useEffect(() => {
        setNewSpaceData();
        if (!props.spaceLines.spaceId) {
            setNewSpaceData(props.spaceLines);
        }

        return () => {
            setNewSpaceData();
        };
    }, [props.spaceLines]);

    // currently this method is not using
    const next = () => {
        const spaces = props.event_space.spaces;
        const vipDuoSelectedSpace = props.event_space.vipDuoSelectedSpace;
        const lastPage = !(
            currentSlide - spaces.length < 0 &&
            currentSlide - spaces.length >= -(slidersize - vipDuoSelectedSpace.length)
        );
        // sortspaces reducer to space sort
        // lastPage && this.setState({page :this.state.page+1}, () => this.props.sortSpaces({page: this.state.page, size: (this.state.slidersize - vipDuoSelectedSpace.length)}));
        slider.current.slickNext();
    };

    // currently this method is not using
    const previous = () => {
        const displayLeftArrow = currentSlide !== 0;
        slider.current.slickPrev();
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This Function handles left and right arrow displaying in slider.
     * -----------------------------------------------------------------------------------------------------------------
     * @param {Number} currentSlide Value of current slide
     * @method
     */
    const setArrowDisplay = (currentSlide, pos) => {
        const spaces = props.event_space.spaces;
        const displayLeftArrow = currentSlide !== 0;
        const displayRightArrow = !(
            currentSlide - spaces.length < 0 && currentSlide - spaces.length > -2
        );

        setDisplayRightArrow(displayRightArrow);
        setDisplayLeftArrow(displayLeftArrow);
        setCurrentSlide(currentSlide);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description - this method used to reset slider state after 3 sec.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const resetSlider = () => {
        clearTimeout(sliderTimeOut);
        const global = this;
        sliderTimeOut = setTimeout(() => {
            //   props.sortSpaces(1);
            // global.setState({page:1});
            setPage(1);
        }, 3000);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function handles the clicks on the left side arrow
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const fetchLastPage = (page) => {
        // props.sortSpaces(page-1);

        setPage(page - 1);
        resetSlider();
    };
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This Function handles clicks on right side arrow.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Number} page Number of the next page
     * @method
     **/
    const fetchNextPage = (page) => {
        // props.sortSpaces(page+1);
        setPage(page + 1);
        resetSlider();
    };

    // for slider custom settings
    const {active_space} = props;
    var settings = {
        swipe: false,
        dots: false,
        infinite: false,
        speed: 500,
        arrow: true,
        //   slidesToShow: 3.35,
        slidesToShow: 5,
        slidesToScroll: 1,
        nextArrow: (
            <ChevronRightSharpIcon
                color="primary"
                fontSize="large"
                className="space_slider_arrow"
            />
        ),
        prevArrow: (
            <ChevronLeftSharpIcon
                color="primary"
                fontSize="large"
                className="space_slider_arrow"
            />
        ),
        variableWidth: false,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1,
                },
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                },
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 2,
                },
            },
        ],
        //   nextArrow: null,
        //   prevArrow: null,
        //   afterChange: (currentSlide,pos) =>setArrowDisplay(currentSlide, pos),
    };

    const cleardata = {
        space_name: "",
    };

    return (
        <div className="spaceSliderOuter">
            <div className="inner_space_slider">
                <section>
                    <div className="spaceSliderWrap">
                        <Slider {...settings}>
                            {spaceData &&
                            spaceData.map((value, key) => {
                                return (
                                    <SpaceItem
                                        key={key}
                                        value={value}
                                        showSpaceLine={props.showSpaceLine}
                                        deleteSpace={props.deleteSpace}
                                        selectedItem={props.selectedItem}
                                        venueType={props.venueType}
                                    />
                                );
                            })}
                            {props.allowAdd && (
                                <div
                                    onClick={() =>
                                        props.showSpaceLine(newSpaceData ? newSpaceData : "")
                                    }
                                    className="AddSpaceCircle"
                                >
                                    {(_.has(newSpaceData, ["space_name"]) &&
                                        newSpaceData.space_name != "") ||
                                    (_.has(newSpaceData, ["space_short_name"]) &&
                                        newSpaceData.space_short_name != "") ||
                                    (_.has(newSpaceData, ["max_capacity"]) &&
                                        newSpaceData.max_capacity != "") ? (
                                        <div className="AddSpaceCircle">
                      <span className="SpaceName">
                        {newSpaceData.space_name}
                      </span>
                                            <span>{newSpaceData.space_short_name}</span>
                                            <span className="SpacePeopleNumber">
                        {newSpaceData.max_capacity}
                      </span>
                                        </div>
                                    ) : (
                                        <div className="AddSpaceCircle">
                                            <span>Add Space</span>
                                            <AddIcon />
                                        </div>
                                    )}
                                </div>
                            )}
                        </Slider>
                    </div>
                </section>

            </div>
        </div>
    );
};

const mapDispatchToProps = (dispatch) => {
    return {}
}

export default connect(null, mapDispatchToProps)(SliderSPersons);
