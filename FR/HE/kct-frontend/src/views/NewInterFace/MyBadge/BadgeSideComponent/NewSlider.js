import React, {useEffect, useState} from 'react';
import _ from 'lodash';
import PopupClose from "../../../../images/cross.svg";
import Helper from '../../../../Helper';
import EventTagList from '../BadgePopup/EventTagList';
import {Col, Tab, Tabs} from 'react-bootstrap';
import Questions from './Questions';
import Searches from './Searches';
import Intrest from './Interest';
import Profile from './Profile';
import {useTranslation} from 'react-i18next';
import './BadgeSlideComponent.css';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render a horizontal nav tab to show all user details(personal info, tags
 * searches, question) in tab format.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @property {UserBadge} props.item User data
 * @property {OrgTag[]} props.tagData Organiser tags for user
 *
 * @returns {JSX.Element}
 * @constructor
 */
const NewSlider = (props) => {
    const [imageAvail, setImageAvail] = useState(false);
    const {t} = useTranslation('myBadgeBlock')

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will create an image element and get data from 'imgPromise' function to render it on
     * interface.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} url
     * @returns {Promise<unknown>}
     */
    const testImage = (url) => {
        // Define the promise
        const imgPromise = new Promise(function imgPromise(resolve, reject) {
            // Create the image
            const imgElement = new Image();
            // When image is loaded, resolve the promise
            const global = this;
            imgElement.addEventListener("load", function imgOnLoad() {
                resolve(this);
            });
            // When there's an error during load, reject the promise
            imgElement.addEventListener("error", function imgOnError() {
                reject();
            });
            // Assign URL
            imgElement.src = url;
        });
        return imgPromise;
    };

    useEffect(() => {
        testImage(props.item.user_avatar).then(
            function fulfilled(img) {
                setImageAvail(true);
            },

            function rejected() {
                setImageAvail(false);
            }
        );
    }, [])

    const {item} = props;
    let totalName = ''
    let firstNameChange = _.has(item, ['user_fname']) ? item.user_fname : '';
    let lastNameChange = _.has(item, ['user_lname']) ? item.user_lname : '';
    // for first name
    if (_.has(item, ['user_fname_change']) && item.user_fname_change) {
        firstNameChange = item.user_fname_change
    }
    // for last name
    if (_.has(item, ['user_lname_change']) && item.user_lname_change) {
        lastNameChange = item.user_lname_change
    }

    if (Helper.objLength(item)) {
        totalName = `${firstNameChange} 
        ${item.visibility.user_lname == 0 && item.user_lname != undefined ? "" : ` ${lastNameChange}`}`;


        return (
            <div className="badge-popup-video no-texture">
                {props.onBlur &&
                <button
                    type="button"
                    className={"close"}
                    onClick={() => {
                        props.onBlur()
                    }}
                >
                    <img src={PopupClose} />
                </button>
                }
                <div className="BadgeDetailDiv">
                    <Col>
                        <Tabs defaultActiveKey="profile">
                            <Tab style={{opacity: 1}} eventKey="profile" title={t("PROFILE")}>
                                <span className="WhiteSpan"></span>
                                <Profile item={item} />

                            </Tab>
                            <Tab style={{opacity: 1}} eventKey="Interest" title={t("INTERESTS")}>
                                <div className=" p-0 col-md-12 col-lg-12 badge-bottom-content">
                                    <EventTagList
                                        paginate={true}
                                        isSlider={true}
                                        isEditable={false}
                                        isLoading={false}
                                        data={
                                            (item.tags_data && item.tags_data.used_tag)
                                                ? item.tags_data.used_tag
                                                : []
                                        }
                                    />
                                    <Intrest item={item} />
                                </div>
                            </Tab>
                            <Tab style={{opacity: 1}} eventKey="search" title={t("SEARCHES")}>

                                <Searches item={item} />
                            </Tab>
                            <Tab style={{opacity: 1}} eventKey="question" title={t("QUESTIONS")}>
                                <Questions item={item} />
                            </Tab>
                            <Tab style={{opacity: 1}} eventKey="" className="blankSpace" title={t(" ")}>
                                <span className="WhiteSpan"></span>
                            </Tab>
                        </Tabs>
                    </Col>
                </div>
            </div>
        )


    } else {
        return (
            <div className="badge-popup-video">
                {props.onBlur &&
                <button
                    type="button"
                    className={"close"}
                    onClick={() => {
                        props.onBlur()
                    }}
                >
                    <img src={PopupClose} />
                </button>
                }
            </div>
        )
    }
}


export default NewSlider;