import React from 'react'
import ModalBox from '../../../Common/ModalBox/ModalBox'
import "../../SpaceManage/SpaceManage.css"
import SpaceManage from "../../SpaceManage/SpaceManage";
import {useTranslation} from "react-i18next";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a popup component in which we are using 2 sub components-<br>
 * 1.SpaceManageForm(Redux form to create a space)<br>
 * 2.SpacePreview(To preview real time data of a space)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {props} props In props we are getting createSpace form value data from redux-form.
 * @returns {JSX.Element}
 */
let SpacePopup = (props) => {
    const {t} = useTranslation('eventCreate');

    return (
        <ModalBox
            ModalHeading={t('modifySpaces')}
            // handleCloseModal={}
            // saveBtnHandler={handleSaveBtn}
            hideTopCloseIcon={true}
            maxWidth={"800px"}
            leftCssVal={"400px"}
        >
            <SpaceManage
                onSaveSpace={props.onSaveSpace}
                onCancel={props.closePopup}
                spaceIndex={props.spaceIndex}
            />
        </ModalBox>

    )
}

export default SpacePopup