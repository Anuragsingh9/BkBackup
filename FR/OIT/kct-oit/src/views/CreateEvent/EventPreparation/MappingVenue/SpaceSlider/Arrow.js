import React from 'react';
import RightArrow from '../../../../../images/right-arrow.png';
import LeftArrow from '../../../../../images/left-arrow.png';

import {useTranslation} from 'react-i18next';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for Arrow button to navigate in spaces slider.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent for Arrow component
 * @param {String} props.direction Direction of the arrow(Left or Right)
 * @param {Function} props.previous Method to fetch the previous list of items, used with left arrow mostly
 * @param {Function} props.next Method to fetch the next list of items, used with right arrow mostly
 * @return {JSX.Element}
 * @constructor
 */
const Arrow = (props) => {
    const {t} = useTranslation('spaces')
    const {direction, next, previous} = props;
    return (<>
            {direction === 'right' ?
                <div className="video-slider-right-arrow flexbox cursor-pointer">

                    <img className="space-arrows" src={RightArrow} onClick={next} data-for='right_Arrow'
                         data-tip={t("Navigate the spaces")} />
                </div>
                :
                <div className="video-slider-left-arrow flexbox cursor-pointer">

                    <img className="space-arrows" src={LeftArrow} onClick={previous} data-for='left_Arrow'
                         data-tip={t("Navigate the spaces")} />
                </div>
            }
        </>
    )
}
export default Arrow;
