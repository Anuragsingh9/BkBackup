import {Grid} from '@material-ui/core'
import {Skeleton} from '@mui/material'
import React from 'react'

const TechnicalSettingSkeleton = () => {
    return (
        <>
            <Grid container xs={12} className="SwitchDivRow skeletonFlex">
                <Grid item className="Flex-1">
                    <Skeleton variant="circular" width={50} height={50} />
                </Grid>
                <Grid item xs={11} className="Flex-2">
                    <Skeleton variant="text" sx={{fontSize: '1.2rem'}} />
                    <Skeleton variant="text" sx={{fontSize: '0.8rem'}} />&nbsp;
                    <Skeleton variant="rectangular" width={"60%"} height={180} />
                </Grid>
            </Grid>
            <Grid container xs={12} className="SwitchDivRow skeletonFlex">
                <Grid item className="Flex-1">
                    <Skeleton variant="circular" width={50} height={50} />
                </Grid>
                <Grid item xs={11} className="Flex-2">
                    <Skeleton variant="text" sx={{fontSize: '1.2rem'}} />
                    <Skeleton variant="text" sx={{fontSize: '0.8rem'}} />&nbsp;
                    <Skeleton variant="rectangular" width={"60%"} height={180} />
                </Grid>
            </Grid>

        </>
    )
}

export default TechnicalSettingSkeleton