import SenderAddressesForm from "./SenderAddressesForm";
import Box from "@mui/material/Box";
import Card from "@mui/material/Card";
import Skeleton from "@mui/material/Skeleton";
import Typography from "@mui/material/Typography";
import { useSenderAddressesQuery, useShopsQuery } from "../../../queries/settings";
import SettingPrintForm from "../SettingPrintForm";
import {useState} from "react";
import SelectInput from "../Inputs/SelectInput";
import Grid from "@mui/material/Grid";

const SenderAddressesData = () => {

    const shops = useShopsQuery();
    const [selectedId, setSelectedId] = useState(0);
    const [formKey, setFormKey] = useState(1);


  const data = useSenderAddressesQuery(selectedId);
  const nod = (() => {
    if (data)
      return (
        <>
            <SenderAddressesForm key={formKey}
                                 data={data}
                                 storeId={selectedId}
            />
        </>
      );
    else return <Skeleton height={150} sx={{ transform: "scale(1,1)" }} />;
  })();

  return (
    <Card id="etiquete">
      <Box p={2}>
        <Typography variant="h3" marginBottom={4}>
          Etiketa
        </Typography>
          {shops.data ?
              <Grid paddingBottom={2} container alignItems={"center"} justifyContent={"flex-end"}>
                  <Grid item xs={8}>
                      <SelectInput
                          value={`${selectedId}`}
                          disableClearable={true}
                          optionals={shops.data.map(x => ({
                              label: x.name,
                              id: `${x.id}`
                          }))}
                          onChange={id=>{
                            if (id && parseInt(id))
                              setSelectedId(parseInt(id));
                      }} />
                  </Grid>
              </Grid>: null}
        {nod}
      </Box>
      <Box p={2}>
        <Typography variant="h3" marginBottom={4}>
          Tisk
        </Typography>
        <SettingPrintForm />
      </Box>
    </Card>
  );
};

export default SenderAddressesData;
