import { useShipmentSettings, useShopsQuery } from "../../../queries/settings";
import { useEffect, useMemo, useState } from "react";
import Skeleton from "@mui/material/Skeleton";
import Grid from "@mui/material/Grid";
import SelectInput from "../Inputs/SelectInput";
import { Box, Card, Tab, Tabs, Typography } from "@mui/material";
import { BaseForm } from "./BaseForm";
import { useQueryCountries, useQueryCurrencies, useQueryPayments } from "../../../queries/codelists";

const TabPanel = (props: { children?: React.ReactNode; index: number; value: number }) => {
  const { children, value, index, ...other } = props;

  return (
    <div role="tabpanel" hidden={value !== index} {...other}>
      {value === index && <Box>{children}</Box>}
    </div>
  );
};

const ShipmentSettingData = () => {
  const shops = useShopsQuery();
  const [selectedId, setSelectedId] = useState(0);
  const [formKey, setFormKey] = useState(1);
  const [tabId, setTabId] = useState(0);

  const payments = useQueryPayments();

  const currencies = useQueryCurrencies();

  const countries = useQueryCountries();

  const changeTab = (event: React.SyntheticEvent, newValue: number) => {
    setTabId(newValue);
  };

  useEffect(() => {
    setTabId(0);
  }, [selectedId]);

  const data = useShipmentSettings(selectedId);

  const upgradedData = useMemo(() => {
    if (data && currencies) {
      const newData: typeof data = JSON.parse(JSON.stringify(data));
      newData.map(item => {
        const currentCurrencies = item.currencies;
        currencies.forEach(y => {
          if (!currentCurrencies.some(x => x.currency === y.code))
            currentCurrencies.push({
              currency: y.code,
            });
        });
      });

      newData.push({
        guid: `${new Date().getTime()}`,
        currencies: currencies.map(x => ({ currency: x.code })),
        weights: [],
      });

      return newData;
    }

    return null;
  }, [data, currencies, selectedId]);

  if (!upgradedData || !shops.data || !currencies || !payments || !countries) {
    return <Skeleton />;
  }

  return (
    <Card id="shipmentSetting">
      <Box paddingTop={2} paddingBottom={2} paddingLeft={2} paddingRight={2}>
        <Typography variant="h3" marginBottom={4}>
          Nastavení dopravy
        </Typography>
        <Grid paddingBottom={2} container alignItems={"center"} justifyContent={"flex-end"}>
          <Grid item xs={12}>
            <SelectInput
              multiple={false}
              value={`${selectedId}`}
              disableClearable={true}
              optionals={(shops.data || []).map(x => ({
                label: x.name,
                id: `${x.id}`,
              }))}
              onChange={id => {
                const newid = parseInt(`${id}`);
                if (!isNaN(newid)) setSelectedId(newid);
              }}
            />
          </Grid>
        </Grid>
        <Grid item xs={12}>
          <Tabs value={tabId} onChange={changeTab} aria-label="basic tabs example">
            {upgradedData.map((x, index) => {
              let title = x.title;
              if (index === upgradedData.length - 1) {
                title = "Nový";
              } else if (!title) {
                title = `${index + 1}`;
              }
              return <Tab label={title} tabIndex={index} />;
            })}
          </Tabs>
          {upgradedData.map((x, index) => {
            return (
              <TabPanel index={index} value={tabId}>
                <BaseForm
                  payments={payments}
                  countries={countries}
                  data={x}
                  asNew={upgradedData.length - 1 === index}
                  storeId={selectedId}
                />
              </TabPanel>
            );
          })}
        </Grid>
      </Box>
    </Card>
  );
};

export default ShipmentSettingData;
