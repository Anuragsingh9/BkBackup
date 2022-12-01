
import React, {useEffect, useState} from 'react';
import { Radio } from '@mui/material';
import {Button,IconButton,Grid} from '@mui/material';
import { DataGrid } from '@material-ui/data-grid';
import SearchBar from '../../../Common/SearchBar/SearchBar';
import TrashIcon from "../../../Svg/TrashIcon";
import Select from "@material-ui/core/Select";
import Hstatus from "../../common/HStatus/Hstatus";
import MouseOverPopover from "../../common/HoverPopup/HoverPopup";




const rows = [{
    name: "Roxie Thorny",
    id: 1,
    company:"Jaxworks, Training",
    position: "Training,Devpoint",
    union: "Apiaceae,Devpoint, Training",
    role: "Project Manager",
    email: "rwatmough0@sbwire.com"
  }, {
    name: "Thorny Roxie",
    id: 2,
    company:"Jatri, Training",
    position: "Product Management",
    union: "Asteraceae,Devpoint, Training",
    role: "Architect",
    email: "tpinwell1@unc.edu"
  }, {
    name: "Ferdie Thorny",
    id: 3,
    company:"Trudoo, Training",
    position: "Human Resources",
    union: "Asteraceae,Devpoint, Training",
    role: "Architect",
    email: "fdurgan2@purevolume.com"
  }, {
    name: "Dacy Thorny",
    id: 4,
    company:"Fadeo, Training",
    position: "Research and Development",
    union: "Fabaceae,Devpoint, Training",
    role: "Construction Worker",
    email: "ddavydychev3@ftc.gov"
  }, {
    name: "Brady Thorny",
    id: 5,
    company:"Viva, Training",
    position: "Research and Development",
    union: "Rubiaceae,Devpoint, Training",
    role: "Construction Worker",
    email: "bnacey4@unesco.org"
  }, {
    name: "Amelita Thorny",
    id: 6,
    company:"Feedspan, Training",
    position: "Research and Development",
    union: "Iridaceae,Devpoint, Training",
    role: "Supervisor",
    email: "aherrieven5@princeton.edu"
  }, {
    name: "Marcelline Thorny",
    id: 7,
    company:"Jatri, Training",
    position: "Sales",
    union: "Fabaceae,Devpoint, Training",
    role: "Subcontractor",
    email: "mpipe6@ox.ac.uk"
  }, {
    name: "Nomi Thorny",
    id: 8,
    company:"Skimia, Training",
    position: "Legal",
    union: "Poaceae,Devpoint, Training",
    role: "Electrician",
    email: "nord7@deviantart.com"
  }, {
    name: "Evelin Thorny",
    id: 9,
    company:"Ozu, Training",
    position: "Marketing",
    union: "Portulacaceae,Devpoint, Training",
    role: "Architect",
    email: "ecrabtree8@diigo.com"
  }, {
    name: "Ailyn Thorny",
    id: 10,
    company:"Devpoint, Training",
    position: "Research and Development",
    union: "Bacidiaceae,Devpoint, Training",
    role: "Architect",
    email: "afirbank9@ezinearticles.com"
  }];


  /**
   * @class
   * @component
   * 
   * -------------------------------------------------------------------------------------------------------------------
   * @description This is a list component to show all group memabers in a tabular form.
   * -------------------------------------------------------------------------------------------------------------------
   * 
   * @returns {JSX.Element}
   */
const ManageAllList = () => {
    const [mode, setMode] = useState(0);
    const [columns, setColumns] = useState([]);

    useEffect(() => {
        setCurrentColumn();
    },[])
    const allColumns ={
        name:{
            field: "name",
            headerName: "Name",
            width: 220,
            sortable: false,
            className:"text_primary_col",
            headerAlign: 'center',
            align: 'center',
    
        },
        id:{
            field: 'id',
            headerName: 'HStatus',
            width: 120,
            headerAlign: 'center',
            sortable: false,
            editable: false,
            renderCell: (cellValues) => {
                return (
                    <Hstatus />
                );
            }
        },
        company:{
            field: 'company',
            headerName: 'Company',
            width: 220,
            headerAlign: 'center',
            sortable: false,
            editable: false,
        },
        position:{
            field: 'position',
            headerName: 'Position',
            width: 180,
            headerAlign: 'center',
            sortable: false,
            editable: false,
        },
        union:{
            field: 'union',
            headerName: 'Union',
            width: 260,
            sortable: false,
            headerAlign: 'center',
            editable: false,
        },
        role:{
            field: 'role',
            headerName: 'Role',
            width: 150,
            headerAlign: 'center',
            sortable: false,
            editable: false,
            renderCell: (cellValues) => {
                return (
                    <MouseOverPopover />
                );
            }
        },
        email:{
            field: 'email',
            headerName: 'Email',
            width: 360,
            headerAlign: 'center',
            sortable: false,
            editable: false,
        }
    };
    const setCurrentColumn = () => {
        let newColumns = [];

        newColumns.push(allColumns.name);
        newColumns.push(allColumns.id);
        newColumns.push(allColumns.company);
        newColumns.push(allColumns.union);
        newColumns.push(allColumns.email);
        // newColumns.push(allColumns.role);
        
        setColumns(newColumns);
    }
    
    return (
        <div style={{ height: 650, width: '100%', }} className='ManageAllListDiv'>
            <Grid container specing={0} className="TableSearchBar" >
                <Grid >
                    <Select
                        native
                        value={mode}
                        onChange={(e) => {
                            setMode(e.target.value)
                        }}
                        name="number"
                        size="small"
                        className="selectCustom"
                        variant="outlined"
                        inputProps={{'aria-label': 'age'}}
                    >

                        <option value="">Add Users</option>
                        <option value={1}>Manually</option>
                        <option value={2}>Import</option>

                    </Select>
                </Grid>
                {/* <Button onClick={setCurrentColumn}>click</Button> */}
                <Grid >
                    <IconButton aria-label="delete" size="large">
                        <TrashIcon />
                    </IconButton>
                    &nbsp;&nbsp;
                    <SearchBar type={'regular'} />
                </Grid>
            </Grid>
            
            <DataGrid
                rows={rows}
                columns={columns}
                pageSize={10}
                rowsPerPageOptions={[10,20,30,40,50,60,70,80,90,100]}
                checkboxSelection={true}
                disableSelectionOnClick
                disableColumnMenu={true}
                sortable={false}
                // density="compact"
            />
        </div>
    );
}
export default ManageAllList;
