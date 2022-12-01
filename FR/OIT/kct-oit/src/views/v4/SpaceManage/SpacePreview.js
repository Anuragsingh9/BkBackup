import React from 'react'
import {connect} from "react-redux";
import {getFormValues} from 'redux-form';
import {useTranslation} from 'react-i18next';

let SpacePreview = (props) => {
    const {t} = useTranslation("addSpaceForm");
    console.log('dsdsd', props)
    return (
        <>
            <div className='spaceCircle'>
                <div
                    className={
                        `roundSpace spaceSelected  
                    ${props?.spaceFormValues?.space_is_vip ? "vipSpace" : "normalSpace"}`
                    }
                >
                    <small>{t("youAreHere")}</small>
                    <span className="SpaceName">
                        {props?.spaceFormValues?.space_line_1 || "--"}
                    </span>
                    <span>
                        {props?.spaceFormValues?.space_line_2 || "--"}
                    </span>
                    <span className="SpacePeopleNumber">{props?.spaceFormValues?.space_max_capacity || 0}</span>
                    <small>{t("guest")}</small>
                </div>
            </div>
        </>
    )
}

const mapStateToProps = (state) => {
    return {
        spaceFormValues: getFormValues('createSpaceForm')(state),
    }
}

SpacePreview = connect(mapStateToProps)(SpacePreview)
export default SpacePreview