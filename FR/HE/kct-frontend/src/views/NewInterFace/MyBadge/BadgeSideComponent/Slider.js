import React from 'react';
import _ from 'lodash';
import PopupClose from "../../../../images/cross.svg";
import Helper from '../../../../Helper';
import ReactTooltip from "react-tooltip";
import EventTagList from '../BadgePopup/EventTagList';
import ShowUserBadge from './ShowUserBadge';


/**
 * @deprecated
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to show a slider effect component for user badge(where all details are
 * displaying of a user eg - name, tags, company, union, profile picture)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 */
class Slider extends React.Component {
    state = {
        imageAvail: false,
        listHeight: 96,
        mock: {
            personal_info: {field_1: "This is updated", field_2: "Looking forrrrr", field_3: "question ?"},
            visibility: {p_field_1: 1, p_field_2: 1, p_field_3: 1}
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will check the there is no number in the input value(name_words)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} name_words
     * @returns {*}
     */
    first_last_name = (name_words) => {
        var str = name_words;
        if (str !== undefined) {
            var matches = str.match(/\b(\w)/g);
            return matches.join("");
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will create an image element and get data from 'imgPromise' function to render it on
     * interface.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} url
     * @returns {Promise<unknown>}
     */
    testImage = (url) => {
        // Define the promise
        const imgPromise = new Promise(function imgPromise(resolve, reject) {
            // Create the image
            const imgElement = new Image();
            // When image is loaded, resolve the promise
            const global = this;
            imgElement.addEventListener("load", function imgOnLoad() {
                resolve(this);
                // global.setState({imageAvail:true});
            });
            // When there's an error during load, reject the promise
            imgElement.addEventListener("error", function imgOnError() {
                reject();
                // global.setState({imageAvail:true});
            });
            // Assign URL
            imgElement.src = url;
        });
        return imgPromise;
    };

    componentDidMount() {
        const global = this;
        this.testImage(this.props.item.user_avatar).then(
            function fulfilled(img) {
                global.setState({imageAvail: true});
            },

            function rejected() {
                global.setState({imageAvail: false});
            }
        );
    }


    render() {
        const {item} = this.props;
        let totalName = ''
        let firstNameChange = _.has(item, ['user_fname']) ? item.user_fname : '';
        let lastNameChange = _.has(item, ['user_lname']) ? item.user_lname : '';
        if (_.has(item, ['user_fname_change']) && item.user_fname_change) {
            firstNameChange = item.user_fname_change
        }
        if (_.has(item, ['user_lname_change']) && item.user_lname_change) {
            lastNameChange = item.user_lname_change
        }

        if (Helper.objLength(item)) {
            if (_.has(item, ['visibility'])) {
                totalName = `${firstNameChange} ${item.visibility.user_lname == 0
                && item.user_lname != undefined
                    ? ""
                    : `${lastNameChange}`
                }`;

                const companyName = _.has(item.company, ['long_name'])
                    ? `${item.company.long_name}${item.company.position
                        ? `, ${item.company.position}`
                        : ''}`
                    : '';
                const unionName = !_.isEmpty(item.unions)
                    ? `${_.last(item.unions).long_name}${_.last(item.unions).position
                        ? `, ${_.last(item.unions).position}`
                        : ''}`
                    : '';
                const fieldName_1 = !_.isEmpty(item.personal_info.field_1) ? `${item.personal_info.field_1}` : '';
                const fieldName_2 = !_.isEmpty(item.personal_info.field_2) ? `${item.personal_info.field_2}` : '';
                const fieldName_3 = !_.isEmpty(item.personal_info.field_3) ? `${item.personal_info.field_3}` : '';
                return (
                    <div className="badge-popup-video no-texture">
                        {/* <ReactTooltip type="dark" effect="solid" id={`abc`}/> */}
                        {this.props.onBlur &&
                        <button
                            type="button"
                            className={"close"}
                            onClick={() => {
                                this.props.onBlur()
                            }}
                        >
                            <img src={PopupClose} />
                        </button>
                        }
                        <div className="row">
                            <div className="col-md-4 col-lg-3 col-sm-4">
                                {item.user_avatar ?

                                    <img
                                        className="badge-avtar no-texture"
                                        width="95px"
                                        height="93px"
                                        src={item.user_avatar}
                                    />

                                    :
                                    <div
                                        className="username-slider-dp no-texture">
                                        {Helper.nameProfile(item.user_fname, item.user_lname)}
                                    </div>
                                }
                            </div>
                            <div className="col-md-8 col-lg-9 col-sm-8  badge-right-content">
                                <h2
                                    className="d-inline-block w-100 mx-0 badge-name"
                                    title={totalName.length > 16 ? totalName : ''}
                                    //  data-for={`abc`}
                                >
                                    {Helper.limitText(totalName, 16)}
                                </h2>
                                {item.visibility.company == 0
                                    ? " "
                                    : (item.company
                                        && _.has(item.company, ['long_name'])
                                        && <p
                                            className="d-inline-block mx-0 "
                                            title={companyName.length > 25 ? companyName : ''}
                                        >
                                            {Helper.limitText(companyName, 25)}
                                        </p>)}
                                <br />
                                {
                                    item.visibility.unions == 0
                                        ? " "
                                        : (item.unions
                                            && !_.isEmpty(item.unions)
                                            && <p
                                                className="d-inline-block mx-0 "
                                                title={unionName.length > 25 ? unionName : ''}
                                            >
                                                {Helper.limitText(unionName, 25)}
                                            </p>)}
                            </div>
                            <div className="col-md-12 col-lg-12 badge-bottom-content">
                                <EventTagList
                                    paginate={true}
                                    isSlider={true}
                                    isEditable={false}
                                    isLoading={false}
                                    data={(item.tags_data && item.tags_data.used_tag) ? item.tags_data.used_tag : []}
                                />
                                <ShowUserBadge item={item} />
                            </div>
                        </div>
                    </div>
                )
            } else {
                totalName = `${firstNameChange} ${lastNameChange}`;

                const companyName = _.has(item.company, ['long_name'])
                    ? `${item.company.long_name}${item.company.position
                        ? `, ${item.company.position}`
                        : ''}`
                    : '';
                const unionName = !_.isEmpty(item.unions)
                    ? `${item.unions[0].long_name}${item.unions[0].position
                        ? `, ${item.unions[0].position}`
                        : ''}`
                    : '';
                return (
                    <div className="badge-popup-video no-texture">
                        <ReactTooltip type="dark" effect="solid" id={`abc`} />
                        {this.props.onBlur &&
                        <button
                            type="button"
                            className={"close"}
                            onClick={() => {
                                this.props.onBlur()
                            }}>
                            <img src={PopupClose} />
                        </button>
                        }
                        <div className="row">
                            <div className="col-md-4 col-lg-3">
                                {item.user_avatar ?
                                    <img className="badge-avtar no-texture" src={item.user_avatar} />
                                    :
                                    <div
                                        className="username-slider-dp no-texture">
                                        {Helper.nameProfile(item.user_fname, item.user_lname)}
                                    </div>
                                }
                            </div>
                            <div className="col-md-8 col-lg-9 badge-right-content">
                                <h2
                                    className="d-inline-block w-100 mx-0 badge-name"
                                    title={totalName.length > 16 ? totalName : ''}
                                    data-for={`abc`}
                                >
                                    {Helper.limitText(totalName, 16)}                                </h2>
                                {
                                    item.company
                                    && _.has(item.company, ['long_name'])
                                    && <p
                                        className="d-inline-block mx-0 "
                                        title={companyName.length > 25 ? companyName : ''}
                                    >
                                        {Helper.limitText(companyName, 25)}
                                    </p>}
                                <br />
                                {
                                    item.unions
                                    && !_.isEmpty(item.unions)
                                    && <p
                                        className="d-inline-block mx-0 b"
                                        title={unionName.length > 25 ? unionName : ''}
                                    >
                                        {Helper.limitText(unionName, 25)}
                                    </p>
                                }
                                <br />
                                <EventTagList
                                    paginate={true}
                                    isEditable={false}
                                    isLoading={false}
                                    data={(item.tags_data && item.tags_data.used_tag) ? item.tags_data.used_tag : []}
                                />
                                <ShowUserBadge item={item} />
                            </div>
                        </div>
                    </div>
                )
            }


        } else {
            return (
                <div className="badge-popup-video">
                    {this.props.onBlur &&
                    <button
                        type="button"
                        className={"close"}
                        onClick={() => {
                            this.props.onBlur()
                        }}
                    >
                        <img src={PopupClose} />
                    </button>
                    }
                </div>
            )
        }
    }
};

export default Slider;