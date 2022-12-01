import React from 'react';
import Box from '@mui/material/Box';
import Modal from '@mui/material/Modal';
import {Button, Grid} from '@material-ui/core';
import Typography from '@mui/material/Typography';
import _ from 'lodash';
import "./Modal.css";

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
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is developed to show a modal box to take final confirmation from user to perform a certain action
 * (currently it is used to reset all colors value of a sub/child design setting section in the form of primary colors).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @deprecated
 * @returns {JSX.Element}
 * @constructor
 */
const KeepMountedModal = (props) => {

  return (
    <div>
      <Button color="primary" onClick={props.handleOpen}  disabled={props?.disabled}>{props.btn_txt}</Button>
      <Modal
        keepMounted
        open={props.open}
        onClose={props.handleClose}
        aria-labelledby="keep-mounted-modal-title"
        aria-describedby="keep-mounted-modal-description"
      >
        <Box sx={style}>
          <Typography id="keep-mounted-modal-title" variant="h6" component="h2">
            Are you sure?
          </Typography>
          <Typography id="keep-mounted-modal-description" sx={{mt: 2}}>
            <span className='modal_info_txt' >
              "Reset to primary colors"
            </span>
            will replace all the custom colors of this component to primary colors.
            <span className='modal_info_txt'>
              {props.reset_color}
            </span>
          </Typography>
          <Grid container xs={12} className='custom_modal_footer'>
            <Button variant="outlined" color="primary" onClick={props.handleClose}>Cancel</Button>
            <Button variant="contained" color="primary" onClick={props.onclick}>Apply</Button>
          </Grid>
        </Box>
      </Modal>
    </div>
  );
}
export default KeepMountedModal;