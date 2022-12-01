import {Grid} from '@material-ui/core'
import {Skeleton} from '@mui/material'
import React from 'react'

const MediaGridSkeleton = () => {
    let rowNumber = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    return (
        <>
            <Grid container xs={12} className="SwitchDivRow skeletonFlex">
                <Skeleton variant="text" width={280} sx={{fontSize: '2rem'}} />
            </Grid>
            &nbsp;&nbsp;
            <Grid container xs={12} className="SwitchDivRow skeletonFlex">
                {rowNumber.map(e => <><Skeleton variant="rectangular" width={150} height={90} />&nbsp;&nbsp;&nbsp;&nbsp;</>)}
            </Grid>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <Grid container xs={12} className="SwitchDivRow skeletonFlex">
                <Skeleton variant="text" width={280} sx={{fontSize: '2rem'}} />
            </Grid>
            &nbsp;&nbsp;
            <Grid container xs={12} className="SwitchDivRow skeletonFlex">
                {rowNumber.map(e => <><Skeleton variant="rectangular" width={150} height={90} />&nbsp;&nbsp;&nbsp;&nbsp;</>)}
            </Grid>

        </>
    )
}

export default MediaGridSkeleton