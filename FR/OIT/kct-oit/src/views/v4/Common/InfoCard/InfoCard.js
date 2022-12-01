import * as React from 'react';
import Card from '@mui/material/Card';
import CardActions from '@mui/material/CardActions';
import CardContent from '@mui/material/CardContent';


const InfoCard = (props) => {
    return (
        <Card className={props?.className || ''} elevation={2} style={{width: '100%',height: '100%'}}>
            {
                props?.children &&
                <CardContent>
                    {props.children}
                </CardContent>
            }
            {
                props?.cardActions &&
                <CardActions>
                    {props.cardActions}
                </CardActions>
            }
        </Card>
    );
}
export default InfoCard
