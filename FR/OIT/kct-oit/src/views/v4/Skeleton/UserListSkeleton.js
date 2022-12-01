import {Grid} from '@material-ui/core'
import {Skeleton} from '@mui/material'
import React from 'react'

const UserListSkeleton = () => {
    let rowNumber = [1, 2, 3, 4, 5, 6];
    return (
        <div>
            <Skeleton variant="text" width={150} sx={{fontSize: '1.2rem'}} />
            
            <Skeleton variant="text" sx={{fontSize: '4rem'}} />
            <Skeleton variant="text" sx={{fontSize: '2rem'}} />
            <hr></hr>
            {rowNumber.map(e => <Skeleton variant="text" sx={{fontSize: '2rem'}} />)}
            <hr></hr>
        </div>
    )
}

export default UserListSkeleton