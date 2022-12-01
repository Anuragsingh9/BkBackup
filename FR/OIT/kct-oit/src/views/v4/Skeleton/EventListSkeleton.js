import {Grid} from '@material-ui/core'
import {Skeleton} from '@mui/material'
import React from 'react'

const EventListSkeleton = () => {
    let rowNumber = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    return (
        <div>&nbsp;&nbsp;
            <Skeleton variant="text" width={150} sx={{fontSize: '1.2rem'}} />
            &nbsp;&nbsp;
            <Skeleton variant="text" sx={{fontSize: '4rem'}} />
            <Skeleton variant="text" sx={{fontSize: '2rem'}} />
            <hr></hr>
            {rowNumber.map(e => <Skeleton variant="text" sx={{fontSize: '2rem'}} />)}
            <hr></hr>
        </div>
    )
}

export default EventListSkeleton