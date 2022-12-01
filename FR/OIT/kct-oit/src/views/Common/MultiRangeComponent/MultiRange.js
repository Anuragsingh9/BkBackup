import React, {useEffect, useState} from 'react';
import './style.css';
import _ from 'lodash';
import ArrowDropDownIcon from '@mui/icons-material/ArrowDropDown';
import moment from "moment-timezone";
import Tooltip from "@material-ui/core/Tooltip";

var prevX = -1;

/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to show number of moments(networking and content + networking) on a custom
 * designed range component.In this component user can figure the no of moments with their respective start and end time
 * <br>
 * <br>
 * This component is used in key moment(2nd step of event creation process for content+networking event)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Array} props.data Event details
 * @param {String} props.date Event date
 * @param {Object} props.total Event time
 * @param {String} props.total.start_time Event start time
 * @param {String} props.total.end_time Event start time
 * @param {String} props.type Type of event( Content, Content+Networking)
 * @returns {JSX.Element}
 * @constructor
 */
const MultiRange = (props) => {
    console.log('props1111', props)

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to map the  data from props
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const data = props.data.map((item) => {
        return {
            name: item.name,
            start: moment(`${item.date} ${item.start_time}`)._d,
            end: moment(`${item.date} ${item.end_time}`)._d
        }
    });

    // this is used for start date and end date
    const total = {
        start: moment(`${props.date} ${props.total.start_time}`)._d,
        end: moment(`${props.date} ${props.total.end_time}`)._d
    }

    const [steps, setSteps] = useState(0);
    const [ranges, setRanges] = useState([]);

    useEffect(() => {
        let difference = getTimeDiff(total.end, total.start) / 10;

        setSteps(difference);

        generatedSelectedSteps(difference);

    }, [props.data]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for generated step from time
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const generatedSelectedSteps = () => {
        const rangeArray = [];

        data.map((item) => {
                const startPoint = total.start.getTime() > item.start.getTime()
                    ? 0
                    : getTimeDiff(total.start, item.start) / 10;
                const endPoint = getTimeDiff(total.start, item.end) / 10;
                const type = props.type ? props.type : ''
                rangeArray.push({start: startPoint, end: endPoint, name: item.name, type: type});
            }
        )
        setRanges(rangeArray);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to convert date into time
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} item Object of date and time
     * @returns {string}
     */
    const convertDateToTime = (item) => {
        const date = moment(item);
        return date.format('hh:mm A');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Adding minutes in time
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} dt Object of Date and Time
     * @param {String} minutes Minutes in the time
     */
    const add_minutes = (dt, minutes) => {
        return moment(dt.getTime() + minutes * 60000)._d;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Shows tool tips on steps
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} step Value of moment block
     * @returns {string}
     */
    const getToolTip = (step) => {
        const minutes = step * 10;
        const newTime = add_minutes(total.start, minutes);
        return convertDateToTime(newTime)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the time difference
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} dt2 Object of Date and Time
     * @param {Object} dt1 Object of Date and Time
     * @returns {number}
     */
    const getTimeDiff = (dt2, dt1) => {
        var diff = (dt2.getTime() - dt1.getTime()) / 60000;
        return Math.abs(Math.round(diff));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This is used for calculate color
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} val Value of moment block
     * @param {Number} i Number of current block
     * @returns {Number}
     */
    const calculateColor = (val, i) => {
        return `${(1 - (val - (i - 1))) * 100}`
    }

    /**
     * @deprecated
     */
    const onChangeStartTime = (e) => {
        if (prevX == -1) {
            prevX = e.pageX;
            return false;
        }
        // dragged left
        if (prevX > e.pageX) {
            // console.log('dragged left', prevX - e.pageX, 'moved');
        } else if (prevX < e.pageX) { // dragged right
            // console.log('dragged right', e.pageX - prevX, 'moved');
        }
        prevX = e.pageX;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will return the first block of a moment for multi range slider component.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} momentBlock Object of moment block
     * @param {Number} momentNumber Number of moment block
     * @returns {JSX.Element}
     */
    const prepareStartBlock = (momentBlock, momentNumber) => {
        return <Tooltip arrow title={<div>
            <spam>{momentBlock.name}</spam>
            <br />
            <spam>{momentBlock.type}</spam>
        </div>} placement="top-start">
            <div className={`block start-mark ${momentNumber} ${!props.content ? 'SelectedContent' : ''}`}
                 title={momentBlock.name}>
                <div className="start_tooltip">{`${getToolTip(momentBlock.start)}`}
                    <ArrowDropDownIcon />
                </div>
            </div>
        </Tooltip>
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will return a tooltip component to show moment's start details.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} momentBlock Object of moment block
     * @param {Number} momentNumber Number of moment block
     * @returns {JSX.Element}
     */
    const prepareEndBlock = (momentBlock, momentNumber) => {
        return <div className={`block end-mark ${momentNumber}`}>
            <div className="end_tooltip">{`${getToolTip(momentBlock.end)}`}
                <ArrowDropDownIcon />
            </div>
        </div>
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will return a slider component which consist some blocks as per event duration.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {Array}
     */
    const renderSlider = () => {
        let block = [];
        let foundStart = false;
        let blockBluePrint = [];

        const blockType = {
            networking: 'Networking',
            content: 'Content',
        }

        let blockColor = {};

        blockColor[blockType.networking] = 'selectedDiv SelectedContent';
        blockColor[blockType.content] = 'selectedDiv';

        for (let currentBlock = 0; currentBlock < steps; currentBlock++) {
            blockBluePrint.push({});
        }

        ranges.forEach((momentBlock, mNumber) => {
            for (let currentBlock = momentBlock.start; currentBlock < momentBlock.end; currentBlock++) {
                if (_.isEmpty(blockBluePrint[currentBlock])) {
                    blockBluePrint[currentBlock] = {
                        type: momentBlock.type,
                        color: momentBlock.type === blockType.networking ? 'blue' : 'content',
                        isStart: currentBlock === momentBlock.start,
                        isEnd: currentBlock === momentBlock.end - 1,
                        names: [
                            momentBlock.name,
                        ],
                        start: momentBlock.start,
                        end: momentBlock.end,
                        momentNumber: mNumber,
                    }
                } else {
                    blockBluePrint[currentBlock].names.push(momentBlock.name);
                }
            }
        });


        blockBluePrint.forEach(blockPrint => {
            if (_.isEmpty(blockPrint)) {
                // there is nothing to show, so just push empty block
                block.push(<div
                    className={`block`}>
                </div>);
            } else if (blockPrint.isStart && blockPrint.isEnd) {
                block.push(
                    <Tooltip arrow title={<div>
                        <spam>{blockPrint.names[0]}</spam>
                        <br />
                        <spam>{blockPrint.type}</spam>
                    </div>} placement="top-start">
                        <div className={`block start-mark ${blockColor[blockPrint.type]}`}>
                            <div className="start_tooltip">{`${getToolTip(blockPrint.start)}`}
                                <ArrowDropDownIcon />
                            </div>
                            <div className="end_tooltip">{`${getToolTip(blockPrint.end)}`}
                                <ArrowDropDownIcon />
                            </div>
                        </div>
                    </Tooltip>
                )
            } else if (blockPrint.isStart) {
                block.push(
                    <Tooltip arrow title={<div>
                        <spam>{blockPrint.names[0]}</spam>
                        <br />
                        <spam>{blockPrint.type}</spam>
                    </div>} placement="top-start">
                        <div className={`block start-mark ${blockColor[blockPrint.type]}`}>
                            <div className="start_tooltip">{`${getToolTip(blockPrint.start)}`}
                                <ArrowDropDownIcon />
                            </div>
                        </div>
                    </Tooltip>
                )
            } else if (blockPrint.isEnd) {
                block.push(
                    <div className={`block end-mark ${blockColor[blockPrint.type]}`}
                         title={blockPrint.names[0]}>
                        <div className="end_tooltip">{`${getToolTip(blockPrint.end)}`}
                            <ArrowDropDownIcon />
                        </div>
                    </div>
                );
            } else {
                block.push(
                    <div
                        className={`block ${blockColor[blockPrint.type]}`}>
                    </div>
                )
            }

        })
        return block;

        for (let currentBlock = 0; currentBlock < steps; currentBlock++) {
            block.push(
                <div
                    className={`block selectedDiv SelectedContent`}>
                </div>
            )
        }
        // return block;

        block = [];

        for (let currentBlock = 0; currentBlock < steps; currentBlock++) {
            const newData = ranges.filter((momentBlock, momentNumber) => {

                if (currentBlock === momentBlock.start
                    && (momentBlock.start > momentBlock.end || momentBlock.start === momentBlock.end)) {
                    momentBlock.end = momentBlock.start + 1;
                    block.push(
                        <div
                            className={`block selectedDiv SelectedContent`}>
                        </div>
                    )
                }
                if (currentBlock === momentBlock.start) {
                    foundStart = true;
                    block.push(prepareStartBlock(momentBlock, momentNumber))
                    return momentBlock;
                } else if (currentBlock == momentBlock.end) {
                    foundStart = false;
                    const nextStart = ranges[momentNumber + 1];
                    if (!(currentBlock == momentBlock.end && nextStart && currentBlock == nextStart.start)) {
                        // push nothing as next moment block have start from here
                        block.push(prepareEndBlock(momentBlock, momentNumber))
                        return momentBlock;
                    }
                } else if (momentBlock.start < currentBlock && currentBlock - 1 < momentBlock.start) {
                    block.push(
                        <Tooltip arrow title={<div>
                            <spam>{momentBlock.name}</spam>
                            <br />
                            <spam>{momentBlock.type}</spam>
                        </div>} placement="top-start">
                            <div className={`block start-mark-${momentNumber}`}
                                 title={calculateColor(momentBlock.start, currentBlock)}>
                            </div>
                        </Tooltip>
                    )
                } else if (momentBlock.end < currentBlock && momentBlock.end > currentBlock - 1) {
                    block.push(
                        <Tooltip arrow title={<div>
                            <spam>{momentBlock.name}</spam>
                            <br />
                            <spam>{momentBlock.type}</spam>
                        </div>} placement="top-start">

                            <div className={`block end-mark-${momentNumber}`}
                                 title={calculateColor(momentBlock.end, currentBlock)}>
                            </div>
                        </Tooltip>
                    )
                } else if ((momentBlock.start <= currentBlock && currentBlock <= momentBlock.end)) {
                    block[currentBlock] =
                        <Tooltip arrow title={<div>
                            <spam>{momentBlock.name}</spam>
                            <br />
                            <spam>{momentBlock.type}</spam>
                        </div>} placement="top-start">
                            <div className={`block selectedDiv ${!props.content ? 'SelectedContent' : ''}`}>
                            </div>
                        </Tooltip>
                }
            })

            if (_.isEmpty(newData)) {
                block.push(
                    <div
                        className={
                            `block ${foundStart ? `selectedDiv ${!props.content ? 'SelectedContent' : ''}` : ''}`
                        }>
                    </div>
                )
            }
        }


        return block;
    }

    /**
     * @deprecated
     */
    const renderSliderData = () => {
        ranges.map((item) => {
            return (
                <input type="range" />
            )
        })

    }

    /**
     * @deprecated
     */
    const prepareMarker = () => {
        let markers = []
        for (let i = 0; i <= steps; i++) {
            markers.push({
                value: i,
                label: `${i * 10}`,
            })
        }
        return markers;
    }

    /**
     * @deprecated
     */
    const prepareValues = () => {
        return ranges.map((item) => {
            return item.start
        })
    }

    /**
     * @deprecated
     */
    const valuetext = (i) => {
        return `${i * 10}`;
    }

    return (
        <div className="RangeDivWrap">
            <div className="slidercontainer">
                {renderSlider()}
            </div>
            <div className="rangeSliderLabel">
                <span className="customPara">{moment(props.total.start_time, ["HH.mm"]).format("hh:mm A")}</span>
                <span className="customPara">{moment(props.total.end_time, ["HH.mm"]).format("hh:mm A")}</span>
            </div>
        </div>
    )
}

export default MultiRange;