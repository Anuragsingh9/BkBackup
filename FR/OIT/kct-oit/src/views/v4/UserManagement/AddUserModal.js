import ModalBox from "../../Common/ModalBox/ModalBox";
import AddUser from "../../CreateEvent/EventPreparation/ManagingRoles/AddUserParticipants/AddUser";
import ImportUser from "../../UserSettings/ImportUser";
import React from "react";
import {useParams} from "react-router-dom";
import Constants from "../../../Constants";
import './AddUserModal.css'

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for show add user modal
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
const AddUserModal = (props) => {
    const params = useParams();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for set the default value for(add user manually, import user)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const callBack = () => {
        props.updateAddUserPopUpDisplay(false,0,true)
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for set the default value for(add user manually, import user and recurrence popup)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleClosePopup = () => {
        props.updateAddUserPopUpDisplay(false,0,true);
    }

    return (
        <div >
            {/*To show Add user manually*/}
            {props.mode === Constants.addUserType.ADD_USER && props.addUserModalOpen !== false && (
                <div>
                    <ModalBox
                        ModalHeading="Add User Manually"
                        // showFooter
                        hideTopCloseIcon={false}
                        handleCloseModal={handleClosePopup}
                        isShowSaveBtn={false}
                        maxWidth={'30vw'}
                        leftCssVal={'15vw'}
                        topCssVal={'15vh'}
                        // maxHeight={'88vh'}
                    >
                        <AddUser
                            event_uuid={params.event_uuid}
                            callBack={callBack}
                            gKey={params.gKey}
                            setFetch={props.setFetch}
                        />

                    </ModalBox>
                </div>
            )}

            {/*To show import user modal*/}
            {props.mode === Constants.addUserType.IMPORT_USER && props.addUserModalOpen !== false && (
                <div>
                    <ModalBox
                        ModalHeading="Import User"
                        // showFooter
                        handleCloseModal={handleClosePopup}
                        hideTopCloseIcon={false}
                        isShowSaveBtn={false}
                        maxWidth={'90vw'}
                        leftCssVal={'48vw'}
                        topCssVal={'1vh'}
                        maxHeight={'88vh'}
                        fixedBodyHeight={"fixedBodyHeight"}
                    >
                        <ImportUser
                            event_uuid={params.event_uuid}
                            callBack={callBack}
                        />
                    </ModalBox>
                </div>
            )}
        </div>
    )
}

export default AddUserModal;