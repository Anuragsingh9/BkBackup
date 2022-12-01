import React from "react";
import Button from "@mui/material/Button";
import groupAction from "../../../../../redux/action/apiAction/group";
import {connect} from "react-redux";
import Helper from "../../../../../Helper";
import _ from 'lodash'


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component to render in modal box (which is open when use click on "delete" option from dropdown split
 * button)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Object that contains all information to show in split button component.
 * @param {Function} props.deleteGroup Function to delete a group.
 * @param {Function} props.key Unique key for the specific group.
 * @param {GroupObj} props.group_data All available groups data.
 * @param {Function} props.reloadGroup Function to reload data as per pagination.
 * @returns {JSX.Element}
 */
const DeleteExportContent = (props) => {
    const handlePermanent = () => {
        props.setIsPermanentDelete(true);
    };

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method send data for delete and export user
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onDelete = () => {
        const data = {
            "_method": "DELETE",
            "group_key": props.group_key,
            "delete_mode": 1
        };
        console.log("sending delete data", data)
        props.deleteGroup(data);
        props.handleCloseModal()
    }


    return (
        <div className="modalContent--reusable">
            <p>
                Are you sure you want to delete group? Group users will be exported to
                the "{props.default_group_data.default_group.name}" Group.
            </p>
            <div className="modalFooter--reusable">
                <Button variant="contained" color="primary" className="theme__containbutton__color" onClick={onDelete}>
                    Delete and Export users
                </Button>
                &nbsp;&nbsp;
                <Button variant="outlined" color="primary" className="theme__outlinebutton__color"
                        onClick={handlePermanent}>
                    Delete Permanently
                </Button>
            </div>
        </div>
    );
};


export default DeleteExportContent;
