import { Fragment, useEffect, useMemo, useState } from "react";
import { components } from "../../../schema";
import { useForm, Controller, useFieldArray, FormProvider } from "react-hook-form";
import Grid from "@mui/material/Grid";
import FormLabel from "@mui/material/FormLabel";
import TextField from "@mui/material/TextField";
import TextFieldController from "../Controllers/TextFieldController";
import { WeightsForm } from "./WeightsForm";
import Switch from "@mui/material/Switch";
import { Button, FormHelperText } from "@mui/material";
import { useQueryCurrencies } from "../../../queries/codelists";
import CountryController from "../Controllers/CountryController";
import SelectInput from "../Inputs/SelectInput";
import { useShipmentSettigMutation, useShipmentSettigRemovingMutation } from "../../../queries/settings";
import { ValidationErrorException } from "../../../queries/types";

export const BaseForm = (props: {
  data: components["schemas"]["ShipmentMethodSettingModel"];
  countries: components["schemas"]["CountryModel"][];
  payments: string[];
  asNew: boolean;
  storeId: number;
}) => {
  const form = useForm<typeof props.data>({
    values: props.data,
  });

  const { control, getValues, watch, handleSubmit } = form;
  const parcelBoxes = watch("parcelBoxes");

  const [save, setSave] = useState(false);
  const [deleted, setDeleted] = useState(false);

  const settingMutation = useShipmentSettigMutation(props.storeId);
  const settingRemovingMutation = useShipmentSettigRemovingMutation(props.storeId);

  let submitButton = "";

  const onSubmit = async (data: components["schemas"]["ShipmentMethodSettingModel"]) => {
    setSave(true);
    try {
      if (submitButton === "save") {
        await settingMutation.mutateAsync(data);
      } else {
        await settingRemovingMutation.mutateAsync(data);
      }
    } catch (errors) {
      if (errors instanceof ValidationErrorException) {
        const data = errors.data;
        Object.keys(data.errors).forEach(x => {
          // @ts-ignore
          setError(`data.${x}`, { type: "validation", message: data.errors[x][0] });
        });
      }
    } finally {
      setSave(false);
    }
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <FormProvider {...form}>
        <Grid container alignItems={"center"}>
          <Grid item xs={4} display={"flex"} alignContent={"center"}>
            <FormLabel>Titulek</FormLabel>
          </Grid>
          <Grid item xs={8} display={"flex"}>
            <TextFieldController control={control} name={"title"} />
          </Grid>
          <Grid item xs={4} display={"flex"} alignContent={"center"}>
            <FormLabel>Popis</FormLabel>
          </Grid>
          <Grid item xs={8} display={"flex"}>
            <TextFieldController control={control} name={"description"} />
          </Grid>
          <Grid item xs={4} display={"flex"} alignContent={"center"}>
            <FormLabel>Parcelboxy</FormLabel>
          </Grid>
          <Grid item xs={8} display={"flex"}>
            <Controller
              control={control}
              name="parcelBoxes"
              render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                return (
                  <Switch
                    readOnly={true}
                    checked={!!value}
                    value={value}
                    name={"parcelBoxes"}
                    onChange={e => {
                      onChange(!value);
                    }}
                  />
                );
              }}
            />
          </Grid>
          {parcelBoxes ? (
            <>
              <Grid item xs={4} display={"flex"} alignContent={"center"}>
                <FormLabel>Nepoužívat parcelboxy</FormLabel>
              </Grid>
              <Grid item xs={8} display={"flex"}>
                <Controller
                  control={control}
                  name="disabledParcelBox"
                  render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                    return (
                      <>
                        <Switch
                          readOnly={true}
                          checked={!!value}
                          value={value}
                          name={"disabledParcelBox"}
                          onChange={e => {
                            onChange(!value);
                          }}
                        />
                        {error?.message ? <FormHelperText error={true}>{error?.message}</FormHelperText> : null}
                      </>
                    );
                  }}
                />
              </Grid>
              <Grid item xs={4} display={"flex"} alignContent={"center"}>
                <FormLabel>Nepoužívat alzaboxy</FormLabel>
              </Grid>
              <Grid item xs={8} display={"flex"}>
                <Controller
                  control={control}
                  name="disabledAlzaBox"
                  render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                    return (
                      <>
                        <Switch
                          readOnly={true}
                          checked={!!value}
                          value={value}
                          name={"disabledAlzaBox"}
                          onChange={e => {
                            onChange(!value);
                          }}
                        />
                        {error?.message ? <FormHelperText error={true}>{error?.message}</FormHelperText> : null}
                      </>
                    );
                  }}
                />
              </Grid>
              <Grid item xs={4} display={"flex"} alignContent={"center"}>
                <FormLabel>Nepoužívat parcelshopy</FormLabel>
              </Grid>
              <Grid item xs={8} display={"flex"}>
                <Controller
                  control={control}
                  name="disabledParcelShop"
                  render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                    return (
                      <>
                        <Switch
                          readOnly={true}
                          checked={!!value}
                          value={value}
                          name={"disabledParcelShop"}
                          onChange={e => {
                            onChange(!value);
                          }}
                        />
                        {error?.message ? <FormHelperText error={true}>{error?.message}</FormHelperText> : null}
                      </>
                    );
                  }}
                />
              </Grid>
            </>
          ) : null}
          <Grid item xs={4} display={"flex"} alignContent={"center"}>
            <FormLabel>Povolené země</FormLabel>
          </Grid>
          <Grid item xs={8} display={"flex"}>
            <Controller
              control={control}
              name="countries"
              render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                return (
                  <SelectInput
                    multiple
                    onMultipleChange={ids => {
                      onChange(ids);
                    }}
                    optionals={props.countries
                      .filter(y => {
                        if (parcelBoxes) return y.parcelAllowed;
                        return true;
                      })
                      .map(x => {
                        return {
                          id: x.code,
                          label: x.title,
                        };
                      })}
                    multipleValue={value || []}
                  />
                );
              }}
            />
          </Grid>
          <Grid item xs={4} display={"flex"} alignContent={"center"}>
            <FormLabel>Zakázané platby</FormLabel>
          </Grid>
          <Grid item xs={8} display={"flex"}>
            <Controller
              control={control}
              name="disablePayments"
              render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                return (
                  <SelectInput
                    multiple
                    onMultipleChange={ids => {
                      onChange(ids);
                    }}
                    optionals={props.payments.map(x => {
                      return {
                        id: x,
                        label: x,
                      };
                    })}
                    multipleValue={value || []}
                  />
                );
              }}
            />
          </Grid>
          <Grid item xs={4} display={"flex"} alignContent={"center"}>
            <FormLabel>Platba dobírkou</FormLabel>
          </Grid>
          <Grid item xs={8} display={"flex"}>
            <Controller
              control={control}
              name="codPayment"
              render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                return (
                  <SelectInput
                    onChange={ids => {
                      onChange(ids);
                    }}
                    optionals={props.payments.map(x => {
                      return {
                        id: x,
                        label: x,
                      };
                    })}
                    value={value || undefined}
                  />
                );
              }}
            />
          </Grid>
          <Grid item xs={4} display={"flex"} alignContent={"center"}>
            <FormLabel>Platba podle váhy</FormLabel>
          </Grid>
          <Grid item xs={8} display={"flex"}>
            <Controller
              control={control}
              name="costByWeight"
              render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                return (
                  <>
                    <Switch
                      readOnly={true}
                      checked={!!value}
                      value={value}
                      name={"costByWeight"}
                      onChange={e => {
                        onChange(!value);
                      }}
                    />
                    {error?.message ? <FormHelperText error={true}>{error?.message}</FormHelperText> : null}
                  </>
                );
              }}
            />
          </Grid>
          <WeightsForm data={props.data} />
          <Grid item xs={12}>
            <Button
              onClick={e => {
                submitButton = "save";
              }}
              type="submit"
              value="save"
              disabled={deleted || save}
            >
              Uložit
            </Button>
            {!props.asNew ? (
              <Button
                onClick={e => {
                  submitButton = "delete";
                }}
                type="submit"
                value="delete"
                color="error"
                disabled={deleted || save}
              >
                Smazat
              </Button>
            ) : null}
          </Grid>
        </Grid>
      </FormProvider>
    </form>
  );
};
