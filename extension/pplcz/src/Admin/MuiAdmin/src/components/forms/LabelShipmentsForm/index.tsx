import Alert from "@mui/material/Alert";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import FormLabel from "@mui/material/FormLabel";
import Grid from "@mui/material/Grid";
import Step from "@mui/material/Step";
import StepLabel from "@mui/material/StepLabel";
import Stepper from "@mui/material/Stepper";
import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";
import { components } from "../../../schema";
import Item from "./Item";
import { useLabelPrintSettingQuery, useLabelPrintSettingMutation } from "../../../queries/settings";
import { useQueryLabelPrint } from "../../../queries/codelists";
import { Controller, FormProvider, useFieldArray, useForm } from "react-hook-form";
import createLabels from "./createLabels";
import prepareShipments from "./prepareShipments";
import refreshLabels from "./refreshLabels";
import { useCallback, useEffect, useMemo, useState } from "react";
import SelectPrint from "../Inputs/SelectPrint";
import { makePrintUrl } from "../../../connection";
import { useRefreshBatch } from "../../../queries/useBatchQueries";
import { DndProvider } from "react-dnd";
import { HTML5Backend } from "react-dnd-html5-backend";

type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

type CreteLabelShipmentItems = {
  labelPrintSetting: string;
  items: ShipmentWithAdditionalModel[];
};

export const LabelShipmentForm = (props: {
  batchId: string;
  models: ShipmentWithAdditionalModel[];
  hideOrderAnchor?: boolean;
  onFinish?: () => void;
  onRefresh?: (orderIds: number[]) => void;
}) => {
  const [step, setStep] = useState(-1);
  const currentPrint = useLabelPrintSettingQuery();
  const availableValues = useQueryLabelPrint();

  const refreshBatch = useRefreshBatch(props.batchId);

  const values = useMemo(() => {
    return {
      labelPrintSetting: currentPrint.data || "",
      items: props.models,
    } as CreteLabelShipmentItems;
  }, [props.models]);

  const form = useForm<CreteLabelShipmentItems>({
    values,
  });

  const [create, setCreate] = useState(false);

  const [locked, setLocked] = useState(props.models[0].shipment.lock);

  const { getValues, setError, watch, clearErrors, setValue } = form;

  const [url, setUrl] = useState<URL | null>(null);

  const labelPrintMutation = useLabelPrintSettingMutation();

  const [flashId, setStateFlashId] = useState<string | null>(null);

  const printState = watch("labelPrintSetting");

  const setFlashId = useMemo(() => {
    let timeout: any = null;
    return (id: string | null) => {
      if (timeout) {
        clearTimeout(timeout);
        timeout = null;
      }

      if (id) {
        timeout = setTimeout(() => {
          setStateFlashId(null);
        }, 800);
      }
      setStateFlashId(id);
    };
  }, []);

  const printUrl = useMemo(() => {
    if (props.models[0].shipment.lock && props.models[0].shipment.packages?.[0]?.shipmentNumber) {
      return new URL(makePrintUrl(props.batchId, null, null, printState));
    }

    if (url) return url;

    return null;
  }, [url, props.models, printState]);

  const [errorMessage, setErrorMessage] = useState("");

  const [message, setMessage] = useState("");

  useEffect(() => {
    if (currentPrint.data) {
      form.setValue("labelPrintSetting", currentPrint.data);
    }
  }, [currentPrint.data || ""]);

  useEffect(() => {
    props.models.forEach((x, index) => {
      if (x.errors?.length) {
        const error = x.errors.shift();
        setError(`items.${index}`, {
          type: "server",
          message: error?.values[0],
        });
      }
    });
  }, [currentPrint.data || ""]);

  useEffect(() => {
    if (create || locked) {
      const controller = new AbortController();
      let values = form.getValues("items");

      if (create) {
        labelPrintMutation.mutateAsync({
          printState,
        });
      }

      clearErrors();
      setErrorMessage("");

      (async () => {
        if (create) {
          setStep(1);
          setMessage("Probíhá validace zásilek");
          let prepared = values.map(shipment => ({
            orderId: shipment.shipment.orderId,
            shipmentId: shipment.shipment.id,
          }));

          try {
            if (await prepareShipments(props.batchId, prepared, controller, setError, setValue)) {
              props.onRefresh?.(prepared.map(x => x?.orderId || 0).filter(x => !!x));
            } else {
              setCreate(false);
              setErrorMessage("Při validaci zásilek došlo k chybě");
              setMessage("");
              return;
            }
          } catch (e) {
            setErrorMessage("Neočekávaný problém");
            setMessage("");
            setCreate(false);
            setLocked(false);
          }

          setStep(2);
          values = form.getValues("items");
          setMessage("Probíhá příprava etiket");
          try {
            await createLabels(
              props.batchId,
              values.map(x => x.shipment.id!),
              getValues("labelPrintSetting"),
              controller,
              setError,
              setValue
            );
          } catch (e) {
            props.onRefresh?.(values.map(x => x?.shipment?.orderId || 0).filter(x => !!x));
            setErrorMessage("Při vytváření zásilek došlo k chybě");
            setMessage("");
            setCreate(false);
            setLocked(false);
            return;
          }
        }

        if (create || locked /* && !printUrl*/) {
          setStep(3);
          setMessage("Čekáme na vytvoření etiket");
          try {
            const url = await refreshLabels(
              props.batchId,
              values.map(x => x.shipment.id!),
              getValues("labelPrintSetting"),
              controller,
              setValue
            );
            await refreshBatch();
            setMessage("");
            if (url) {
              setUrl(url);
            } else {
              setErrorMessage("Při získávání skupinového tisku došlo k chybě");
            }
            props.onRefresh?.(values.map(x => x?.shipment.orderId || 0).filter(x => !!x));
          } catch (e) {
            props.onRefresh?.(values.map(x => x?.shipment.orderId || 0).filter(x => !!x));
            setErrorMessage("Při získávání skupinového tisku došlo k chybě");
            setMessage("");
          }
          setCreate(false);
          setLocked(false);
        }
      })();
    }
  }, [create, locked]);

  const { control } = form;
  const { fields, swap } = useFieldArray({
    control,
    name: "items",
  });
  return (
    <>
      <FormProvider {...form}>
        {!printUrl ? (
          <Box p={2}>
            {step >= -1 ? (
              <Box p={2}>
                <Stepper activeStep={step}>
                  <Step key={1}>
                    <StepLabel>Validace</StepLabel>
                  </Step>
                  <Step key={2}>
                    <StepLabel>Vytvoření zásilek</StepLabel>
                  </Step>
                  <Step key={3}>
                    <StepLabel>Příprava etiket</StepLabel>
                  </Step>
                </Stepper>
              </Box>
            ) : null}
            {errorMessage ? <Alert severity="warning">{errorMessage}</Alert> : null}
            {message ? <Alert severity="success">{message}</Alert> : null}
          </Box>
        ) : null}
        <Box className="modalBox">
          <Grid
            container
            spacing={2}
            p={2}
            sx={{
              justifyContent: "center",
              alignItems: "center",
            }}
          >
            <Grid item xs={5}>
              <center>
                <Controller
                  key={JSON.stringify(availableValues)}
                  control={control}
                  name="labelPrintSetting"
                  render={({ field: { value, onChange } }) => {
                    return (
                      <>
                        <Grid container>
                          <Grid item flexGrow={1}>
                            <FormLabel>Formát tisku</FormLabel>
                            <SelectPrint
                              key={JSON.stringify(availableValues)}
                              onChange={e => {
                                onChange(e);
                              }}
                              value={value}
                              optionals={availableValues.data || []}
                            />
                          </Grid>
                        </Grid>
                      </>
                    );
                  }}
                />
                {printUrl ? (
                  <Button
                    onClick={e => {
                      window.open(printUrl, "_blank");
                    }}
                  >
                    Stáhnout štítky
                  </Button>
                ) : (
                  <Button
                    disabled={create || !!locked}
                    onClick={e => {
                      e.preventDefault();
                      setCreate(true);
                    }}
                  >
                    {errorMessage ? "Zkusit znovu" : "Vytisknout zásilky"}
                  </Button>
                )}
              </center>
            </Grid>
          </Grid>
          <DndProvider backend={HTML5Backend}>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>Obj.</TableCell>
                  <TableCell>Adresa</TableCell>
                  <TableCell>Služba</TableCell>
                  <TableCell>Balíků</TableCell>
                  <TableCell>Dobírka</TableCell>
                  <TableCell>VS</TableCell>
                  {props.models[0].shipment.lock ? null : <TableCell>Pozice</TableCell>}
                  {props.models[0].shipment.lock ? null : <TableCell></TableCell>}
                </TableRow>
              </TableHead>
              <TableBody>
                {fields.map((field, index) => (
                  <Item
                    key={field.shipment.id}
                    batchId={props.batchId}
                    swap={swap}
                    maxRows={props.models.length}
                    hideOrderAnchor={props.hideOrderAnchor}
                    position={index}
                    flashId={flashId}
                    setFlashId={setFlashId}
                  />
                ))}
              </TableBody>
            </Table>
          </DndProvider>
        </Box>
      </FormProvider>
    </>
  );
};

export default LabelShipmentForm;
