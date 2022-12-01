import {Grid, Typography} from '@material-ui/core'
import React from 'react'
import {useTranslation} from 'react-i18next';
import NoDataChartIcon from '../../../Svg/NoDataChartIcon'

const NoDataCard = (props) => {
    const {t} = useTranslation(['common', 'analytics']);
    return (
        <Grid
            container
            justifyContent="center"
        >
            <NoDataChartIcon />
            <Typography sx={{mb: 1.5}}
                style={{"color":"#b8b8b8"}}
                className={"appFont nocahartTxt"}
                align={"center"}
            >
                {t(`analytics:${props.infotext || 'no_conversation_in_occurrence'}`)}
            </Typography>
        </Grid>
    )
}

export default NoDataCard