import * as React from 'react';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogTitle from '@mui/material/DialogTitle';
import useMediaQuery from '@mui/material/useMediaQuery';
import {useTheme} from '@mui/material/styles';
import "./DialogBox.css";

/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component of modal which gives a basic functionality for opening and closing modal
 * which can be close with the help of cross icon.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Object} props.actionsButtons JSX component object
 * @param {Object} props.child JSX component Object
 * @param {Function} props.setDialogBoxOpen Dialog box open/close state handler function
 * @param {Boolean} props.dialogBoxOpen Dialog box open/close state
 * @returns {JSX.Element}
 * @constructor
 */
function ResponsiveDialog(props) {
    const {setDialogBoxOpen, dialogBoxOpen} = props;
    const theme = useTheme();
    const fullScreen = useMediaQuery(theme.breakpoints.down('md'));
    const handleClose = () => {
        setDialogBoxOpen(false);
    };

    return (
        <div className='customDialogBox'>
            {/* <Button variant="outlined" onClick={handleClickOpen}>
                Open responsive dialog
            </Button> */}
            <Dialog
                fullScreen={fullScreen}
                open={dialogBoxOpen}
                onClose={handleClose}
                aria-labelledby="responsive-dialog-title"
            >
                <DialogTitle id="responsive-dialog-title">
                    {"Select area to crop image"}.
                </DialogTitle>
                <DialogContent>
                    {props.subHeahing && <p style={{margin: " 0 0 6px 0"}}>{props.subHeahing}</p>}
                    <div className='crop_dialog_content_box'>
                        {props.child}
                    </div>
                </DialogContent>
                <DialogActions>
                    {/* buttons component coming from cropper component */}
                    {props.actionButtons}
                    {/* <Button autoFocus onClick={handleClose}>
                        Disagree
                    </Button>
                    <Button onClick={handleClose} autoFocus>
                        Agree
                    </Button> */}
                </DialogActions>
            </Dialog>
        </div>
    );
}

export default ResponsiveDialog;