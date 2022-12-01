import React from 'react'
import { Skeleton } from '@mui/material'

const EventDropdownSkeleton = () => {
    return (
        <div className='skeletonWrapper'>
            <Skeleton variant="rectangular" height={26} animation="wave" />
            <Skeleton variant="rectangular" height={26} animation="wave" />
            <Skeleton variant="rectangular" height={26} animation="wave" />
            <Skeleton variant="rectangular" height={26} animation="wave" />
            <Skeleton variant="rectangular" height={26} animation="wave" />
            {/* <hr className='ListSaprator' />
            <Skeleton variant="rectangular" height={26} animation="wave" /> */}
        </div>
    )
}

export default EventDropdownSkeleton