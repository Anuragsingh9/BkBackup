import React from 'react';
import Helper from '../../../../Helper';
import _ from 'lodash';
import Describe from './Describe';
import './BadgeSlideComponent.css';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render user's details(first name last name and the and profile picture)
 * in the user badge component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {UserBadge} props.item User details from the badge to show the user describe
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const Profile = (props) => {
    const {item} = props
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
        // check  visibility
        if (_.has(item, ['visibility'])) {
            totalName = `${firstNameChange} ${item.visibility.user_lname == 0
            && item.user_lname != undefined
                ? ""
                : `${lastNameChange}`
            }`;

            const companyPosition = _.has(item.company, ['position'])
                ? `${item.company.position
                    ? ` ${item.company.position}`
                    : ''}`
                : '';
            const companyName = _.has(item.company, ['long_name']) ? `${item.company.long_name}` : '';

            const unionPosition = !_.isEmpty(item.unions)
                ? `${_.last(item.unions).position
                    ? ` ${_.last(item.unions).position}`
                    : ''}`
                : '';
            const unionName = !_.isEmpty(item.unions) ? `${_.last(item.unions).long_name}` : '';
            return (
                <div className=" mainScroll">
                    <div className="col-sm-3 p-0">
                        {item.user_avatar ?
                            <img
                                className="badge-avtar no-texture"
                                width="95px"
                                height="93px"
                                src={item.user_avatar}
                            />
                            :
                            <div
                                className="username-slider-dp no-texture"
                            >
                                {Helper.nameProfile(item.user_fname, item.user_lname)}
                            </div>
                        }
                    </div>
                    <div className="col-sm-9 nameCol9">
                        <h2
                            className="d-inline-block w-100 mx-0 badge-name"
                            title={totalName.length > 16 ? totalName : ''}>
                            {Helper.limitText(totalName, 16)}
                        </h2>
                        {item.visibility.company == 0 ? " " : (item.company && _.has(item.company, ['long_name']) &&
                            <div
                                className="companyNameDiv"
                            >
                                <p
                                    className="d-inline-block mx-0 "
                                    title={companyName.length > 35 ? companyName : ''}
                                >
                                    {Helper.limitText(companyName, 35)}
                                </p>
                                <br />
                                {companyPosition &&
                                <p
                                    className="d-inline-block mx-0 "
                                    title={companyPosition.length > 35 ? companyPosition : ''}
                                >
                                    {Helper.limitText(companyPosition, 35)}
                                </p>
                                }
                            </div>)}
                        {/* <br /> */}

                        {item.visibility.unions == 0 ? " " : (item.unions && !_.isEmpty(item.unions) &&
                            <div
                                className="unionNameDiv"
                            >
                                <p
                                    className="d-inline-block mx-0 "
                                    title={unionName.length > 35 ? unionName : ''}
                                >
                                    {Helper.limitText(unionName, 35)}
                                </p>
                                <br />
                                <p
                                    className="d-inline-block mx-0 "
                                    title={unionPosition.length > 35 ? unionPosition : ''}
                                >
                                    {Helper.limitText(unionPosition, 35)}</p>
                            </div>)
                        }
                    </div>
                    <div>
                        <Describe item={item} />
                    </div>
                </div>
            )
        }
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
            <div>
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
                        {Helper.limitText(totalName, 16)}
                    </h2>
                    {
                        item.company
                        && _.has(item.company, ['long_name'])
                        && <p
                            className="d-inline-block mx-0 "
                            title={companyName.length > 25 ? companyName : ''}
                        >
                            {Helper.limitText(companyName, 25)}
                        </p>
                    }
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
                </div>
                <div>
                    <Describe item={item} />
                </div>
            </div>)
    }
}

export default Profile;            