import React, {useEffect, useState} from 'react';
import Box from '@mui/material/Box';
import Modal from '@mui/material/Modal';
import {Button, Grid} from '@material-ui/core';
import Typography from '@mui/material/Typography';
import _ from 'lodash';
import ReactDOM from 'react-dom';
import CloseIcon from '@mui/icons-material/Close';
import IconButton from '@mui/material/IconButton';
import "./ModalBox.css";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Style object that contains values for styling the component
 * ---------------------------------------------------------------------------------------------------------------------
 */
const style = {
    position: 'absolute',
    top: '50%',
    left: '50%',
    transform: 'translate(-50%, -50%)',
    width: 400,
    bgcolor: 'background.paper',
    borderRadius: '8px',
    boxShadow: 24,
    p: 4,
};

/**
 * -------------------------------------------------------------------------------------------------------------------
 * @description This component is common component for show model in design setting
 * -------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Inherited from parent component
 * @param {Function} props.onClose Function is used to close the modal
 * @returns {JSX.Element}
 * @constructor
 */
const Backdrop = (props) => {
    return <div className='backdrop--reusable' onClick={props.onClose}></div>
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description  This is used for model overlay
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Inherited from parent component
 * @param {String} props.ModalHeading Heading of the modal
 * @param {Boolean} props.hideTopCloseIcon To hide the top close icon from the modal
 * @param {Function} props.onClose Function is used to close the modal
 * @param {Object} props.children Data of the modal
 * @param {Object} props.showFooter Show footer or not in the modal
 * @param {Function} props.saveBtnHandler Function is used to save data of the modal
 * @returns {JSX.Element}
 * @constructor
 */
const ModalOverlay = (props) => {
    return <div className='modal--reusable'
        style={{
            "max-width": `${props.maxWidth}` || "500px",
            "left": `calc(50% - ${props.leftCssVal || "250px"})`,
            "top": `${props.topCssVal || "16vh"}`,
            "max-height": `${props.maxHeight || "100%"}`
        }}>
        <h2 className='modalHeadingPosition--reusable'>{props.ModalHeading}</h2>
        {props?.hideTopCloseIcon == true ?
            ''
            :
            <span className='modalCross--reusable'>
                <IconButton color="primary" aria-label="upload picture" component="span" onClick={props.onClose}>
                    <CloseIcon> </CloseIcon>
                </IconButton>
            </span>
        }
        <div className={`modalBody--reusable ${props.fixedBodyHeight || ''}`}>{props.children}</div>
        {props.showFooter && <div className='modalFooter--reusable'>
            {
                props.saveBtnHandler && props.isShowSaveBtn === true &&
                <Button
                    variant="contained"
                    color="primary"
                    onClick={props.saveBtnHandler}
                >
                    Save
                </Button>
            }
            &nbsp;&nbsp;
            <Button
                variant="outlined"
                color="primary"
                onClick={props.onClose}
            >
                Close
            </Button>
        </div>}
    </div>
}

/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used to contain modal data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.handleCloseModal Function is used to close the modal
 * @param {String} props.ModalHeading Heading of the modal
 * @param {Object} props.showFooter Show footer or not in the modal
 * @param {Function} props.saveBtnHandler Function is used to save data of the modal
 * @param {Boolean} props.hideTopCloseIcon To hide the top close icon from the modal
 * @param {Object} props.children Data of the modal
 * @returns {JSX.Element}
 * @constructor
 */
const ModalBox = (props) => {

    const portalElement = document.getElementById("overlays");
    return (
        <React.Fragment>
            {ReactDOM.createPortal(<Backdrop onClose={props.handleCloseModal} />, portalElement)}
            {ReactDOM.createPortal(
                <ModalOverlay
                    onClose={props.handleCloseModal}
                    ModalHeading={props.ModalHeading}
                    showFooter={props.showFooter}
                    saveBtnHandler={props.saveBtnHandler}
                    hideTopCloseIcon={props?.hideTopCloseIcon}
                    maxWidth={props.maxWidth}
                    leftCssVal={props.leftCssVal}
                    isShowSaveBtn={props.isShowSaveBtn}
                    topCssVal={props.topCssVal}
                    maxHeight={props.maxHeight}
                    fixedBodyHeight={props.fixedBodyHeight}
                >
                    {props.children}
                </ModalOverlay>, portalElement
            )}
        </React.Fragment>
    )
}
export default ModalBox;