import {Grid} from '@material-ui/core'
import {Skeleton} from '@mui/material'
import React from 'react'

const ProfilePageSkeleton = () => {
    let rowNumber = [1, 2, 3, 4, 5];
    return (
        <div className='profileCardDiv other_profile skeletonProfile'>
            <Skeleton variant="rectangular" width={180} height={180} />

            {rowNumber.map(element => (
                <Grid container xs={12} className="SwitchDivRow skeletonFlex">
                    <Grid item xs={5} className="Flex-1">
                        <Skeleton variant="text" sx={{fontSize: '3rem'}} />
                    </Grid>
                    &nbsp;&nbsp;&nbsp;
                    <Grid item xs={5} className="Flex-2">
                        <Skeleton variant="text" sx={{fontSize: '3rem'}} />
                    </Grid>
                </Grid>
            ))}

        </div>
    )
}

export default ProfilePageSkeleton