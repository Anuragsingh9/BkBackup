import React from 'react'
import {Button, Grid} from '@material-ui/core'
import NavigateBeforeIcon from "@mui/icons-material/NavigateBefore";
import NavigateNextIcon from "@mui/icons-material/NavigateNext";
import moment from "moment-timezone";

const PaginationBar = ({
    handlePreviousPageShow,
    disablePrevious,
    handleNextPageShow,
    disableNext,
    selectedVal
}) => {
    return (
        <>
            <Grid item >
                <Button onClick={handlePreviousPageShow}
                    disabled={disablePrevious}>
                    <NavigateBeforeIcon />
                </Button>
                {moment(selectedVal).format('MMM DD')}
                <Button onClick={handleNextPageShow}
                    disabled={disableNext}>
                    <NavigateNextIcon />
                </Button>
            </Grid>
        </>
    )
}


export default PaginationBar