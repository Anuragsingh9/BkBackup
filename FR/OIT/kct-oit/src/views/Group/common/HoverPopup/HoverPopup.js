import React from "react";
import {Popover,Button} from "@material-ui/core";
import Typography from "@material-ui/core/Typography";
import { makeStyles } from "@material-ui/core/styles";
import "./HoverPopup.css";

// design component
const useStyles = makeStyles(theme => ({
  popover: {
    pointerEvents: "none"
  },
  paper: {
    pointerEvents: "auto",
    padding: theme.spacing(1)
  }
}));

/**
 * 
 * @deprecated
 *  
 */
const MouseOverPopover = (props) => {
  const classes = useStyles();
  const [anchorEl, setAnchorEl] = React.useState(null);


  const handlePopoverOpen = event => {
    setAnchorEl(event.currentTarget);
  };


  const handlePopoverClose = () => {
    setAnchorEl(null);
  };

  const open = Boolean(anchorEl);

  const option = ["Profile", "Delete"];

  return (
    <div onMouseEnter={handlePopoverOpen} onMouseLeave={handlePopoverClose}>
      <Typography
        aria-owns={open ? "mouse-over-popover" : undefined}
        aria-haspopup="true"
      >
        Hover with a Popover.
      </Typography>
      <Popover
        id="mouse-over-popover"
        className={classes.popover}
        classes={{
          paper: classes.paper
        }}
        open={open}
        anchorEl={anchorEl}
        anchorOrigin={{
          vertical: "bottom",
          horizontal: "left"
        }}
        transformOrigin={{
          vertical: "top",
          horizontal: "left"
        }}
        onClose={handlePopoverClose}
        disableRestoreFocus
      >
          {/* <Button variant="text" className="theme-btn" color="primary">
              Button
          </Button> */}
          {option.map((val)=>
                <Button variant="text" className="theme-btn hover_popover_btn" color="primary">
                    {val}
                </Button>
          )}
        
      </Popover>
    </div>
  );
}
export default MouseOverPopover