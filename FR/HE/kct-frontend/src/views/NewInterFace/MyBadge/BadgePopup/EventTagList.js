import React, {useEffect, useRef, useState} from 'react';
import Helper from '../../../../Helper'
import {uniqueId} from 'lodash';
import {renderToString} from 'react-dom/server'
import ReactTooltip from 'react-tooltip';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper component of event tag list(added tags).This will render when user start typing
 * in the field of add event tag in badge editor component and then select a tag from the suggestion list.
 * User can delete tag by just click on it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Boolean} props.paginate To indicate for applying the pagination
 * @param {Boolean} props.isEditable To indicate if tags are editable or not
 * @param {Boolean} props.isLoading To indicate to show the loading icon or not
 * @param {PPTag[]} props.data Data of professional or personal tags
 *
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const EventTagList = (props) => {
    const [showMore, newShowMore] = useState(false);
    const [viewTags, setViewTags] = useState([])
    const listRef = useRef(null);

    useEffect(() => {
        if (document.getElementById("preview-tag")) {
            let element = document.getElementById("preview-tag")
            let showMoreElement = document.getElementById("show-more")
            let childHeight = element.scrollHeight
            if (childHeight > element.clientHeight) {
                showMoreElement && showMoreElement.removeAttribute('style')
            } else {
                showMoreElement && showMoreElement.setAttribute('style', 'display:none')
            }
        }
    }, [])
    const setShowMore = () => newShowMore(!showMore)

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will return a JSX structure to render a tag component(event tag) in badge editor
     * component.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {PPTag} o Selected tag
     * @param {Boolean} isEditable To indicate if tag is modifiable or not
     * @param {Function} onDelete Handler method when the tag is removed from profile
     * @returns {JSX.Element}
     */
    const renderLi = (o, isEditable, onDelete) => {
        return (
            <div
                key={o.id}
                className={
                    ` no-texture
       ${props.type == '1'
                        ? 'professional-pop-tags'
                        : props.type == '2'
                            ? 'personal-pop-tags'
                            : 'pop-tags'
                    }`
                }
            >
                {Helper.jsUcfirst(o.name)}
                {
                    (isEditable) &&

                    <span onClick={() => onDelete(o.id)} className="fa fa-trash"></span>
                }
            </div>)

    }

    /**
     * @deprecated
     */
    const showTags = (list) => {
        let line1 = [], line2 = [], line3 = [], line4 = [];
        let count = 0
        let removeKey = [];
        let cLimit = 49
        if (document.getElementById('preview-tag')) {
        }
        document.getElementById('preview-tag')
        list.sort((a, b) => a.name.length - b.name.length)
        let data = list.map((o, k) => {
            count += o.name.length + 3
            if (count <= cLimit) {
                line1.push(o)
            }
            if (count > cLimit && count <= cLimit * 2) {
                line2.push(o)
            }
            if (count > cLimit * 2 && count <= cLimit * 3) {
                line3.push(o)
            }
            if (count > cLimit * 3) {
                line4.push(o)
            }
        })
        return {line1, line2, line3, line4};
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check for the component conditionally rendering
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const checkRender = () => {
    }

    /**
     * @deprecated
     */
    const renderPaginateTag = () => {
        // let { line1, line2, line3, line4 } = this.showTags(data)
        let {isEditable, onDelete, data} = props
        return (
            <>
                <div id="preview-tag" style={{overflow: 'hidden', maxHeight: "87px"}} className="event-tag-list">
                    {data && data.map((o) => {
                        return renderLi(o, isEditable, onDelete)

                    })}
                </div>
                <div
                    style={{background: "white"}}
                    data-for="tag-view"
                    data-iscapture='true'
                    data-class="show-upper"
                    id="show-more"
                    data-tip={
                        renderToString(
                            <ul id="slider-tag-list" className="event-tag-list">
                                {data && data.map((o) => {
                                    return <li className="site-color pop-tags no-texture visible-tag"
                                               key={o.id + uniqueId()}>
                                        {o.name}

                                    </li>
                                })}
                            </ul>
                        )}
                    className="btn btn-link site-color view-more-tags"
                >
                    <span>View more</span>
                </div>
                <ReactTooltip
                    effect="solid"
                    event="click"
                    id="tag-view"
                    place="right"
                    border={true}
                    type="light"
                    className="slider-white-bg"
                    backgroundColor="#f9f9f9"
                    html={true}
                    clickable={true}
                />
                {
                    checkRender()
                }
            </>
        )
    }


    var {data, paginate} = props
    const newData = data.sort((a, b) => {
        return a.name.toLowerCase().localeCompare(b.name.toLowerCase());
    })
    let {isEditable, onDelete} = props

    return (
        <div id="preview-tag" ref={listRef} className="event-tag-list">
            {newData && newData.map((o) => {
                return renderLi(o, isEditable, onDelete)
            })}
        </div>
    )
}
export default EventTagList;