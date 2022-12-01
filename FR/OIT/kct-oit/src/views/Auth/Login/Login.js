import React,{useEffect} from 'react';
import _ from 'lodash';
import { connect } from 'react-redux';
import userAction from '../../../redux/action/apiAction/user';
import groupAction from '../../../redux/action/apiAction/group';
import userReduxAction from '../../../redux/action/reduxAction/user';
import { useAlert } from 'react-alert';
import Helper from '../../../Helper';
const queryString = require('query-string');

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for Login redirection handling
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.getSelfUserById Function is used to get self user by ID
 * @param {Function} props.setUser Function is used to set user
 * @returns {JSX.Element}
 * @constructor
 */
const Login = (props) => {

    // alert use hook
    const alert = useAlert();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Component lifecycle that make sures get user token data to access the interface.
     * -----------------------------------------------------------------------------------------------------------------
     */
    useEffect(()=>{
        var params = queryString.parse(props.location.search);
        if( _.has(params,['token'])){
            loginCheck(params.token);
        }
    },[])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function triggers api and check it is valid or not.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {string} token  Access token.
     * @method
     */
    const loginCheck = (token) => {
     
        try{

            props.getSelfUserById({token}).then((res)=>{
                
                console.log("ressss", res)
                localStorage.setItem('user_data',JSON.stringify(res.data.data));
                localStorage.setItem('userId',res.data.data.id);
                localStorage.setItem('oitToken',token);
                const groupKey = res.data.data.current_group_key
                    props.getSingleGroup(groupKey).then((grpRes) => {
                            localStorage.setItem('Current_group_data', JSON.stringify(grpRes.data.data));
                        })
                props.setUser(res.data.data)
                props.history && props.history.push(`/${res.data.data.current_group_key}/dashboard`);

            }).catch((err)=>{
                console.log("err",err)
                props.history && props.history.push('/')
                alert.show(Helper.handleError(err),{type:'error'})
            })

        }catch(err){
            alert.show(Helper.handleError(err),{type:'error'})
        }
    }

    return(
        <React.Fragment />
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        getSelfUserById: (id) => dispatch(userAction.getSelfUserById(id)),
        setUser: (data) => dispatch(userReduxAction.setUserData(data)),
        getSingleGroup: (groupKey) => dispatch(groupAction.getSingleGroupData(groupKey))
    }
}

const mapStateToProps = (state) => {
    return {
  
    };
};
export default connect(mapStateToProps, mapDispatchToProps)(Login);