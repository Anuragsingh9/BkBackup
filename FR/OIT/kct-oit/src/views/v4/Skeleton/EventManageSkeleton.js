import {Grid} from '@material-ui/core'
import {Skeleton} from '@mui/material'
import React from 'react'

const EventManageSkeleton = () => {
    let rowNumber = [1, 2, 3, 4];
    return (
        <>
            <Grid container xs={7} className=" flex SwitchDivRow skeletonFlex">
                <Grid item className="Flex-1">
                    <Skeleton variant="circular" width={40} height={40} />
                </Grid>
                <Grid item xs={10} className="Flex-2">
                    <Skeleton variant="text" sx={{fontSize: '3.5rem'}} />
                </Grid>
            </Grid>
            <Grid container xs={4} className=" skeletonFlex">
                <Grid item className="Flex-1">
                    <Skeleton variant="circular" width={40} height={40} />
                </Grid>
                <Grid item xs={5} className="Flex-2">
                    <Skeleton variant="text" sx={{fontSize: '3.5rem'}} />
                </Grid>&nbsp;
                <Grid item xs={5} className="Flex-2">
                    <Skeleton variant="text" sx={{fontSize: '3.5rem'}} />
                </Grid>
            </Grid>
            <Grid container xs={4} className=" skeletonFlex">
                <Grid item className="Flex-1">
                    <Skeleton variant="circular" width={40} height={40} />
                </Grid>
                <Grid item xs={10} className="Flex-2">
                    <Skeleton variant="text" sx={{fontSize: '3.5rem'}} />
                </Grid>
            </Grid>
            {rowNumber.map(element => (
                <Grid container xs={4} className=" skeletonFlex">
                    <Grid item className="Flex-1">
                        <Skeleton variant="circular" width={40} height={40} />
                    </Grid>
                    <Grid item xs={10} className="Flex-2">
                        <Skeleton variant="text" sx={{fontSize: '3.5rem'}} />
                    </Grid>
                </Grid>
            ))}&nbsp;
            <Grid container xs={7} className=" flex SwitchDivRow ">
                <Grid item className="Flex-1">
                    <Skeleton variant="circular" width={40} height={40} />
                </Grid>
                <Grid item xs={10} className="Flex-2">
                    <Skeleton variant="rectangular" height={120} />
                </Grid>
            </Grid>

        </>
    )
}

export default EventManageSkeleton