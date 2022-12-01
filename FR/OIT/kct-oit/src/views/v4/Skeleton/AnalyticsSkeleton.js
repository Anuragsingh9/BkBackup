import {Grid} from '@material-ui/core'
import {Skeleton} from '@mui/material'
import React from 'react'

const AnalyticsSkeleton = () => {
    let rowNumber = [1, 2, 3, 4, 5, 6, 7, 8];
    return (
        <>
            <Grid container xs={12} className="flex-Row analyticsWrap">
                <Grid item lg={12} xs={12}>
                    <Skeleton variant="rectangular" width={"100%"} height={160} />
                </Grid>
            </Grid>
            <br/>
            <Grid container spacing={1} xs={12} className='flex-Row'>
                <Grid item lg={6} xs={12} className='mx-10'>
                    <Skeleton variant="rectangular" width={"100%"} height={250} />
                </Grid>
                <Grid item lg={6} xs={12} className='mx-10'>
                    <Skeleton variant="rectangular" width={"100%"} height={250} />
                </Grid>
            </Grid>
            <br/>
            <Grid container spacing={1} xs={12} className='flex-Row'>
                <Grid item lg={6} xs={12} className='mx-10'>
                    <Skeleton variant="rectangular" width={"100%"} height={250} />
                </Grid>
                <Grid item lg={6} xs={12} className='mx-10'>
                    <Skeleton variant="rectangular" width={"100%"} height={250} />
                </Grid>
            </Grid>
        </>
    )
}

export default AnalyticsSkeleton