import React from 'react';
import ReactTooltip from "react-tooltip";
import {useTranslation} from 'react-i18next';
import RightArrow from '../../../images/right-arrow.png';
import LeftArrow from '../../../images/left-arrow.png';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to navigate the space slider in left and right direction.These arrow will be
 * render when no of space in a event is more then 5.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props for the Arrow component
 * @param {String} props.direction Direction of the arrow (Left or Right)
 * @param {Function} props.previous Method to fetch the previous list of items, used with left arrow mostly
 * @param {Function} props.next Method to fetch the next list of items, used with right arrow mostly
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const Arrow = (props) => {
    const {t} = useTranslation('spaces')
    const {direction, next, previous} = props;
    return (<>
            {direction === 'right' ?
                <div className="video-slider-right-arrow flexbox cursor-pointer">
                    <ReactTooltip type="dark" effect="solid" id='right_Arrow' />
                    <img
                        className="space-arrows"
                        src={RightArrow}
                        onClick={next}
                        data-for='right_Arrow'
                        data-tip={t("Navigate the spaces")}
                    />
                </div>
                :
                <div className="video-slider-left-arrow flexbox cursor-pointer">
                    <ReactTooltip type="dark" effect="solid" id='left_Arrow' />
                    <img
                        className="space-arrows"
                        src={LeftArrow}
                        onClick={previous}
                        data-for='left_Arrow'
                        data-tip={t("Navigate the spaces")}
                    />
                </div>
            }
        </>
    )

}
export default Arrow;
